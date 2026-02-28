<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Batch;
use App\Helpers\OrderHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['vendor', 'createdBy']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%");
            });
        }

        $purchaseOrders = $query->latest()->paginate(20);
        $vendors = Vendor::where('is_active', true)->get();
        $statuses = ['pending', 'ordered', 'received', 'cancelled'];

        return view('admin.purchase-orders.index', compact('purchaseOrders', 'vendors', 'statuses'));
    }

    public function create()
    {
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('category')->orderBy('name')->get();
        return view('admin.purchase-orders.create', compact('vendors', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::create([
                'vendor_id' => $validated['vendor_id'],
                'created_by' => Auth::id(),
                'po_number' => OrderHelper::generatePONumber(),
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $itemTotal = $item['quantity'] * $item['unit_cost'];
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $itemTotal,
                    'received_quantity' => 0,
                ]);
                $total += $itemTotal;
            }

            $purchaseOrder->update(['total_amount' => $total]);

            DB::commit();

            return redirect()->route('admin.purchase-orders.show', $purchaseOrder->id)
                ->with('success', 'Purchase order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['vendor', 'createdBy', 'items.product', 'batches']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Purchase order already received.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:purchase_order_items,id',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.batch_number' => 'nullable|string|max:255',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.manufacturing_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $itemData) {
                $item = PurchaseOrderItem::find($itemData['item_id']);
                if (!$item || $item->purchase_order_id !== $purchaseOrder->id) {
                    continue;
                }

                $receivedQty = $itemData['received_quantity'] ?? 0;
                if ($receivedQty > 0) {
                    // Update received quantity
                    $item->increment('received_quantity', $receivedQty);

                    // Create batch if product tracks batches
                    if ($item->product->track_batch && isset($itemData['batch_number']) && isset($itemData['expiry_date'])) {
                        Batch::create([
                            'product_id' => $item->product_id,
                            'purchase_order_id' => $purchaseOrder->id,
                            'batch_number' => $itemData['batch_number'],
                            'expiry_date' => $itemData['expiry_date'],
                            'manufacturing_date' => $itemData['manufacturing_date'] ?? null,
                            'quantity' => $receivedQty,
                            'available_quantity' => $receivedQty,
                            'cost_price' => $item->unit_cost,
                            'is_expired' => false,
                        ]);
                    }
                }
            }

            // Check if all items are fully received
            if ($purchaseOrder->isFullyReceived()) {
                $purchaseOrder->markAsReceived();
            } else {
                $purchaseOrder->update(['status' => 'ordered']);
            }

            DB::commit();

            return back()->with('success', 'Items received successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error receiving items: ' . $e->getMessage());
        }
    }

    /**
     * Show CSV import form for purchase orders
     */
    public function showImportForm()
    {
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku']);
        return view('admin.purchase-orders.import', compact('vendors', 'products'));
    }

    /**
     * Download CSV template for purchase order import
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="purchase_orders_import_template.csv"',
        ];

        $columns = [
            'vendor_id',
            'order_date',
            'expected_delivery_date',
            'notes',
            'product_sku',
            'product_id',
            'quantity',
            'unit_cost',
        ];

        $vendors = Vendor::where('is_active', true)->orderBy('id')->limit(2)->pluck('id')->toArray();
        $products = Product::where('is_active', true)->orderBy('id')->limit(3)->get(['id', 'sku']);

        $callback = function () use ($columns, $vendors, $products) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $columns);

            $vendor1 = $vendors[0] ?? 1;
            $vendor2 = $vendors[1] ?? $vendor1;
            $product1 = $products[0] ?? null;
            $product2 = $products[1] ?? $product1;

            $sku1 = $product1 ? $product1->sku : 'REPLACE_WITH_SKU';
            $sku2 = $product2 ? $product2->sku : 'REPLACE_WITH_SKU';
            $id1 = $product1 ? (string) $product1->id : '1';

            fputcsv($file, [$vendor1, date('Y-m-d'), date('Y-m-d', strtotime('+7 days')), 'Sample order 1', $sku1, '', '100', '5.50']);
            fputcsv($file, [$vendor1, date('Y-m-d'), date('Y-m-d', strtotime('+7 days')), 'Sample order 1', $sku2, '', '50', '12.00']);
            fputcsv($file, [$vendor2, date('Y-m-d'), date('Y-m-d', strtotime('+14 days')), 'Sample order 2', '', $id1, '200', '5.25']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download all products list as CSV (for reference when creating purchase order imports)
     */
    public function downloadProductsList()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_list_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['product_id', 'product_sku', 'product_name']);

            Product::where('is_active', true)
                ->orderBy('name')
                ->select('id', 'sku', 'name')
                ->chunk(500, function ($products) use ($file) {
                    foreach ($products as $product) {
                        fputcsv($file, [
                            $product->id,
                            $product->sku ?? '',
                            $product->name ?? '',
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import purchase orders from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $errors = [];
        $createdCount = 0;
        $rowNumber = 1;

        try {
            $handle = fopen($path, 'r');

            if ($handle === false) {
                return back()->withErrors(['csv_file' => 'Unable to read CSV file.']);
            }

            // Skip BOM if present
            rewind($handle);
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                fseek($handle, 0);
            }

            $headers = fgetcsv($handle);
            if (!$headers) {
                fclose($handle);
                return back()->withErrors(['csv_file' => 'CSV file is empty or invalid.']);
            }

            $headers = array_map(function ($header) {
                $header = preg_replace('/^\xEF\xBB\xBF/', '', trim($header));
                $header = strtolower(preg_replace('/[\x00-\x1F\x7F]/', '', $header));
                return str_replace(' ', '_', $header);
            }, $headers);

            $requiredHeaders = ['vendor_id', 'order_date', 'quantity', 'unit_cost'];
            $missingHeaders = array_diff($requiredHeaders, $headers);
            $hasProductSku = in_array('product_sku', $headers);
            $hasProductId = in_array('product_id', $headers);
            if (!empty($missingHeaders) || (!$hasProductSku && !$hasProductId)) {
                if (empty($missingHeaders) && !$hasProductSku && !$hasProductId) {
                    $missingHeaders[] = 'product_sku or product_id';
                }
                fclose($handle);
                return back()->withErrors([
                    'csv_file' => 'CSV file is missing required columns: ' . implode(', ', $missingHeaders) . '. Found: ' . implode(', ', $headers)
                ]);
            }

            $columnMap = array_flip($headers);

            // Group rows by PO key (vendor_id + order_date + expected_delivery_date + notes)
            $poGroups = [];

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                if (empty(array_filter($row))) {
                    continue;
                }

                while (count($row) < count($headers)) {
                    $row[] = '';
                }

                $getCol = function ($key) use ($row, $columnMap) {
                    $idx = $columnMap[$key] ?? null;
                    return $idx !== null ? trim($row[$idx] ?? '') : '';
                };

                $vendorId = $getCol('vendor_id');
                $orderDate = $getCol('order_date');
                $productSku = $getCol('product_sku');
                $productId = $getCol('product_id');
                $quantity = $getCol('quantity');
                $unitCost = $getCol('unit_cost');

                if (empty($vendorId) || empty($orderDate) || (empty($productSku) && empty($productId)) || empty($quantity) || empty($unitCost)) {
                    $errors[] = "Row {$rowNumber}: Missing required fields (vendor_id, order_date, product_sku or product_id, quantity, unit_cost)";
                    continue;
                }

                $vendor = Vendor::find($vendorId);
                if (!$vendor || !$vendor->is_active) {
                    $errors[] = "Row {$rowNumber}: Invalid or inactive vendor ID: {$vendorId}";
                    continue;
                }

                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $orderDate) || !strtotime($orderDate)) {
                    $errors[] = "Row {$rowNumber}: Invalid order_date format. Use YYYY-MM-DD.";
                    continue;
                }

                if ($expectedDelivery = $getCol('expected_delivery_date')) {
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expectedDelivery) || !strtotime($expectedDelivery)) {
                        $errors[] = "Row {$rowNumber}: Invalid expected_delivery_date format. Use YYYY-MM-DD.";
                        continue;
                    }
                }

                $product = null;
                if (!empty($productSku)) {
                    $product = Product::where('sku', $productSku)->where('is_active', true)->first();
                    if (!$product && is_numeric($productSku)) {
                        $product = Product::find($productSku);
                    }
                }
                if (!$product && !empty($productId) && is_numeric($productId)) {
                    $product = Product::find($productId);
                }
                if (!$product || !$product->is_active) {
                    $identifier = $productSku ?: $productId ?: 'N/A';
                    $errors[] = "Row {$rowNumber}: Product not found or inactive for SKU/ID: {$identifier}";
                    continue;
                }

                if (!is_numeric($quantity) || (int) $quantity < 1) {
                    $errors[] = "Row {$rowNumber}: Quantity must be a positive integer";
                    continue;
                }

                if (!is_numeric($unitCost) || (float) $unitCost < 0) {
                    $errors[] = "Row {$rowNumber}: Unit cost must be a non-negative number";
                    continue;
                }

                $expectedDelivery = $getCol('expected_delivery_date');
                $notes = $getCol('notes');
                $poKey = "{$vendorId}|{$orderDate}|{$expectedDelivery}|{$notes}";

                if (!isset($poGroups[$poKey])) {
                    $poGroups[$poKey] = [
                        'vendor_id' => (int) $vendorId,
                        'order_date' => $orderDate,
                        'expected_delivery_date' => $expectedDelivery ?: null,
                        'notes' => $notes ?: null,
                        'items' => [],
                    ];
                }

                $poGroups[$poKey]['items'][] = [
                    'product_id' => $product->id,
                    'quantity' => (int) $quantity,
                    'unit_cost' => (float) $unitCost,
                ];
            }

            fclose($handle);

            if (empty($poGroups)) {
                $errorMessage = 'No valid purchase order data found in CSV.';
                if (!empty($errors)) {
                    $errorMessage .= ' Please fix the following and try again: ' . implode(' ', array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $errorMessage .= ' (and ' . (count($errors) - 5) . ' more)';
                    }
                } else {
                    $errorMessage .= ' Ensure your CSV has valid data rows with vendor_id, order_date, product_sku (or product_id), quantity, and unit_cost. Download the products list to get valid SKUs and verify vendor IDs exist.';
                }
                return back()->withErrors(['csv_file' => $errorMessage])->with('import_errors', $errors);
            }

            DB::beginTransaction();

            foreach ($poGroups as $poData) {
                $items = $poData['items'];
                if (empty($items)) {
                    continue;
                }

                $purchaseOrder = PurchaseOrder::create([
                    'vendor_id' => $poData['vendor_id'],
                    'created_by' => Auth::id(),
                    'po_number' => OrderHelper::generatePONumber(),
                    'order_date' => $poData['order_date'],
                    'expected_delivery_date' => $poData['expected_delivery_date'],
                    'status' => 'pending',
                    'notes' => $poData['notes'],
                    'total_amount' => 0,
                ]);

                $total = 0;
                foreach ($items as $item) {
                    $itemTotal = $item['quantity'] * $item['unit_cost'];
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'total_cost' => $itemTotal,
                        'received_quantity' => 0,
                    ]);
                    $total += $itemTotal;
                }

                $purchaseOrder->update(['total_amount' => $total]);
                $createdCount++;
            }

            DB::commit();

            $message = "Successfully created {$createdCount} purchase order(s).";
            if (!empty($errors)) {
                return redirect()->route('admin.purchase-orders.import')
                    ->with('success', $message)
                    ->with('import_errors', $errors);
            }

            return redirect()->route('admin.purchase-orders.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase order CSV import error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['csv_file' => 'An error occurred during import: ' . $e->getMessage()]);
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return redirect()->route('admin.purchase-orders.index')
                ->with('error', 'Cannot delete received purchase order.');
        }

        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }
}
