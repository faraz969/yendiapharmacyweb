<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceRequest;
use App\Models\InsuranceCompany;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\DeliveryZone;
use App\Models\DeliveryAddress;
use App\Helpers\OrderHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InsuranceRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = InsuranceRequest::with(['user', 'approvedBy', 'branch', 'insuranceCompany', 'items']);

        // If branch staff, filter by their branch
        if ($user->isBranchStaff()) {
            $query->where('branch_id', $user->branch_id);
        }

        // Status filter - works independently
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Insurance company filter - works independently
        if ($request->filled('insurance_company_id')) {
            $query->where('insurance_company_id', $request->insurance_company_id);
        }

        // Branch filter - works independently (only for admins)
        if ($request->filled('branch_id') && !$user->isBranchStaff()) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search filter - works independently
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('request_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%")
                      ->orWhere('insurance_number', 'like', "%{$search}%");
                });
            }
        }

        // Date filter - single date takes precedence over date range
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            // Date range filter - from date to date (only if single date is not provided)
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        $requests = $query->latest()->paginate(20)->appends($request->query());
        $statuses = ['pending', 'approved', 'rejected', 'order_created'];
        
        // Get branches for filter (only if admin)
        $branches = $user->isBranchStaff() ? null : \App\Models\Branch::active()->get();

        // Get insurance companies for filter
        $insuranceCompanies = InsuranceCompany::orderBy('name')->get();

        return view('admin.insurance-requests.index', compact('requests', 'statuses', 'branches', 'insuranceCompanies'));
    }

    /**
     * Export insurance requests to CSV (respecting current filters)
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        $query = InsuranceRequest::with(['user', 'approvedBy', 'branch', 'insuranceCompany', 'items']);

        // If branch staff, filter by their branch
        if ($user->isBranchStaff()) {
            $query->where('branch_id', $user->branch_id);
        }

        // Status filter - works independently
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Insurance company filter - works independently
        if ($request->filled('insurance_company_id')) {
            $query->where('insurance_company_id', $request->insurance_company_id);
        }

        // Branch filter - works independently (only for admins)
        if ($request->filled('branch_id') && !$user->isBranchStaff()) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search filter - works independently
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('request_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%")
                      ->orWhere('insurance_number', 'like', "%{$search}%");
                });
            }
        }

        // Date filter - single date takes precedence over date range
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            // Date range filter - from date to date (only if single date is not provided)
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        $requests = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="insurance_requests_' . now()->format('Ymd_His') . '.csv"',
        ];

        $columns = [
            'Request Number',
            'Customer Name',
            'Customer Phone',
            'Customer Email',
            'Insurance Company',
            'Insurance Number',
            'Status',
            'Branch',
            'Items Count',
            'Approved By',
            'Approved At',
            'Created At',
        ];

        $callback = function () use ($requests, $columns) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, $columns);

            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->request_number,
                    $request->customer_name,
                    $request->customer_phone,
                    $request->customer_email,
                    optional($request->insuranceCompany)->name,
                    $request->insurance_number,
                    $request->status,
                    optional($request->branch)->name,
                    $request->items->count(),
                    optional($request->approvedBy)->name,
                    optional($request->approved_at)->format('Y-m-d H:i:s'),
                    optional($request->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(InsuranceRequest $insuranceRequest)
    {
        // Check if branch staff can only see their branch requests
        $user = Auth::user();
        if ($user->isBranchStaff() && $insuranceRequest->branch_id !== $user->branch_id) {
            abort(403, 'You can only view insurance requests for your branch.');
        }
        
        $insuranceRequest->load(['user', 'approvedBy', 'branch', 'insuranceCompany', 'items', 'order', 'deliveryZone', 'deliveryAddress']);
        
        // Get delivery zones for approval form
        $deliveryZones = DeliveryZone::all();
        
        return view('admin.insurance-requests.show', compact('insuranceRequest', 'deliveryZones'));
    }

    public function approve(Request $request, InsuranceRequest $insuranceRequest)
    {
        if ($insuranceRequest->status !== 'pending') {
            return back()->with('error', 'Only pending insurance requests can be approved.');
        }

        // Check if user confirmed approval despite unavailable items
        $forceApprove = $request->has('force_approve') && $request->force_approve == '1';

        // Only check availability for products that exist in our database (have product_id)
        $unavailableItems = [];
        
        foreach ($insuranceRequest->items as $item) {
            // Only check products that have product_id (exist in our database)
            if ($item->product_id) {
                $product = Product::find($item->product_id);
                
                if (!$product) {
                    $unavailableItems[] = $item->product_name . ' (product not found)';
                } elseif (!$product->is_active || $product->is_expired) {
                    $unavailableItems[] = $item->product_name . ' (inactive or expired)';
                } elseif ($product->trackBatch && ($product->totalStock ?? 0) < $item->quantity) {
                    $unavailableItems[] = $item->product_name . ' (insufficient stock: ' . ($product->totalStock ?? 0) . ' available, ' . $item->quantity . ' requested)';
                }
            }
            // Custom products (without product_id) are not checked - they're assumed to be available
        }

        // If there are unavailable items and user hasn't confirmed, show prompt
        if (!empty($unavailableItems) && !$forceApprove) {
            return back()->with('unavailable_items', $unavailableItems)
                         ->with('insurance_request_id', $insuranceRequest->id)
                         ->with('admin_notes', $request->admin_notes);
        }

        $insuranceRequest->approve(Auth::id(), $request->admin_notes);

        $message = 'Insurance request approved. Customer can now select delivery and pay for delivery fee.';
        if (!empty($unavailableItems) && $forceApprove) {
            $message .= ' Note: Some products were unavailable but request was approved anyway.';
        }

        return back()->with('success', $message);
    }

    public function reject(Request $request, InsuranceRequest $insuranceRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($insuranceRequest->status !== 'pending') {
            return back()->with('error', 'Only pending insurance requests can be rejected.');
        }

        $insuranceRequest->reject(Auth::id(), $request->rejection_reason);

        return back()->with('success', 'Insurance request rejected.');
    }

    public function createOrder(Request $request, InsuranceRequest $insuranceRequest)
    {
        if ($insuranceRequest->status !== 'approved') {
            return back()->with('error', 'Only approved insurance requests can create orders.');
        }

            $validated = $request->validate([
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address_id' => 'required_if:delivery_type,delivery|nullable|exists:delivery_addresses,id',
            'delivery_zone_id' => 'required_if:delivery_type,delivery|nullable|exists:delivery_zones,id',
            'delivery_address' => 'required_if:delivery_type,delivery|required_without:delivery_address_id|nullable|string',
        ]);

        // Get delivery address if address_id is provided
        $deliveryAddressText = null;
        if (isset($validated['delivery_address_id'])) {
            $deliveryAddress = \App\Models\DeliveryAddress::find($validated['delivery_address_id']);
            if ($deliveryAddress) {
                $deliveryAddressText = $deliveryAddress->address;
            }
        } else {
            $deliveryAddressText = $validated['delivery_address'] ?? null;
        }

        return DB::transaction(function () use ($insuranceRequest, $validated) {
            // Calculate delivery fee
            $deliveryFee = 0.0;
            if ($validated['delivery_type'] === 'delivery' && isset($validated['delivery_zone_id'])) {
                $deliveryZone = DeliveryZone::find($validated['delivery_zone_id']);
                if ($deliveryZone) {
                    // Cast to float to ensure numeric type
                    $deliveryFee = (float) $deliveryZone->delivery_fee; // Only delivery fee, items are free
                }
            }

            // Create order with only delivery fee
            $order = Order::create([
                'user_id' => $insuranceRequest->user_id,
                'branch_id' => $insuranceRequest->branch_id,
                'delivery_address_id' => $validated['delivery_address_id'] ?? null,
                'delivery_zone_id' => $validated['delivery_zone_id'] ?? null,
                'delivery_type' => $validated['delivery_type'],
                'order_number' => OrderHelper::generateOrderNumber(),
                'status' => 'pending',
                'customer_name' => $insuranceRequest->customer_name,
                'customer_phone' => $insuranceRequest->customer_phone,
                'customer_email' => $insuranceRequest->customer_email,
                'delivery_address' => $deliveryAddressText,
                'subtotal' => 0.0, // Items are free with insurance
                'delivery_fee' => $deliveryFee,
                'discount' => 0.0,
                'total_amount' => $deliveryFee,
            ]);

            // Create order items (with 0 price since insurance covers it)
            foreach ($insuranceRequest->items as $item) {
                $productId = null;
                
                // If item has product_id, use it directly
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product && $product->is_active && !$product->is_expired) {
                        $productId = $product->id;
                    }
                } else {
                    // Try to find product by name for custom products
                    $product = Product::where('name', 'like', "%{$item->product_name}%")
                        ->where('is_active', true)
                        ->where('is_expired', false)
                        ->first();
                    if ($product) {
                        $productId = $product->id;
                    }
                }
                
                // Create order item for all products (including custom ones)
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $item->product_name, // Always store product name
                    'quantity' => $item->quantity,
                    'unit_price' => 0, // Free with insurance
                    'total_price' => 0, // Free with insurance
                ]);
            }

            // Update insurance request
            $insuranceRequest->update([
                'status' => 'order_created',
                'order_id' => $order->id,
                'delivery_address_id' => $validated['delivery_address_id'] ?? null,
                'delivery_zone_id' => $validated['delivery_zone_id'] ?? null,
                'delivery_type' => $validated['delivery_type'],
            ]);

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order created successfully. Customer needs to pay delivery fee only.');
        });
    }
}
