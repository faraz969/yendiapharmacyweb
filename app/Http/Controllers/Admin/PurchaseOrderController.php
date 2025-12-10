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
            'expected_delivery_date' => 'nullable|date|after:order_date',
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
