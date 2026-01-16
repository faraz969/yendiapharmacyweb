<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCompany;
use App\Models\InsuranceRequest;
use App\Models\InsuranceRequestItem;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InsuranceController extends Controller
{
    /**
     * Get all active insurance companies
     */
    public function getCompanies()
    {
        $companies = InsuranceCompany::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $companies,
        ]);
    }

    /**
     * Store a new insurance request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'branch_id' => 'required|exists:branches,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'insurance_number' => 'required|string|max:255',
            'card_front_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'card_back_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'prescription_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        // Handle image uploads
        $cardFrontPath = $request->file('card_front_image')->store('insurance-cards', 'public');
        $cardBackPath = $request->file('card_back_image')->store('insurance-cards', 'public');
        $prescriptionPath = null;
        
        if ($request->hasFile('prescription_image')) {
            $prescriptionPath = $request->file('prescription_image')->store('insurance-prescriptions', 'public');
        }

        // Generate request number
        $requestNumber = InsuranceRequest::generateRequestNumber();

        // Create insurance request
        $insuranceRequest = InsuranceRequest::create([
            'user_id' => Auth::id(),
            'branch_id' => $validated['branch_id'],
            'insurance_company_id' => $validated['insurance_company_id'],
            'request_number' => $requestNumber,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'insurance_number' => $validated['insurance_number'],
            'card_front_image' => $cardFrontPath,
            'card_back_image' => $cardBackPath,
            'prescription_image' => $prescriptionPath,
            'status' => 'pending',
        ]);

        // Create admin notification for new insurance request
        \App\Models\Notification::create([
            'for_admin' => true,
            'insurance_request_id' => $insuranceRequest->id,
            'title' => 'New Insurance Request',
            'message' => "New insurance request #{$insuranceRequest->request_number} from {$insuranceRequest->customer_name}",
            'type' => 'info',
            'link' => route('admin.insurance-requests.show', $insuranceRequest->id),
            'is_active' => true,
            'is_read' => false,
        ]);

        // Create request items
        foreach ($validated['items'] as $item) {
            InsuranceRequestItem::create([
                'insurance_request_id' => $insuranceRequest->id,
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $insuranceRequest->load(['insuranceCompany', 'branch', 'items']);

        return response()->json([
            'success' => true,
            'message' => 'Insurance request submitted successfully. A pharmacist will review your request and contact you soon.',
            'data' => $insuranceRequest,
        ], 201);
    }

    /**
     * Get user's insurance requests
     */
    public function index()
    {
        $user = Auth::user();
        
        $query = InsuranceRequest::with(['insuranceCompany', 'branch', 'items', 'approvedBy', 'order']);
        
        if ($user) {
            $query->where('user_id', $user->id);
        }
        
        $requests = $query->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    /**
     * Get a specific insurance request
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $request = InsuranceRequest::with(['insuranceCompany', 'branch', 'items', 'approvedBy', 'order', 'deliveryZone', 'deliveryAddress'])
            ->where(function($query) use ($user) {
                if ($user) {
                    $query->where('user_id', $user->id);
                }
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $request,
        ]);
    }

    /**
     * Create order from approved insurance request (customer-facing)
     */
    public function createOrder(Request $request, $id)
    {
        $user = Auth::user();
        
        $insuranceRequest = InsuranceRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->findOrFail($id);

        if ($insuranceRequest->order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Order already created for this insurance request.',
            ], 400);
        }

        $validated = $request->validate([
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address_id' => 'nullable|integer',
            'delivery_zone_id' => 'nullable|integer',
            'delivery_address' => 'nullable|string',
        ]);

        // Additional validation for delivery type
        if ($validated['delivery_type'] === 'delivery') {
            if (empty($validated['delivery_zone_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery zone is required for delivery orders.',
                    'errors' => ['delivery_zone_id' => ['Delivery zone is required for delivery orders.']],
                ], 422);
            }
            
            // Verify delivery zone exists
            $deliveryZone = \App\Models\DeliveryZone::find($validated['delivery_zone_id']);
            if (!$deliveryZone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid delivery zone selected.',
                    'errors' => ['delivery_zone_id' => ['Invalid delivery zone selected.']],
                ], 422);
            }
            
            if (!empty($validated['delivery_address_id'])) {
                // Verify address belongs to user
                $deliveryAddress = \App\Models\DeliveryAddress::where('user_id', $user->id)
                    ->find($validated['delivery_address_id']);
                if (!$deliveryAddress) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid delivery address selected.',
                        'errors' => ['delivery_address_id' => ['Invalid delivery address selected.']],
                    ], 422);
                }
            } elseif (empty($validated['delivery_address'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery address is required for delivery orders.',
                    'errors' => ['delivery_address' => ['Delivery address is required for delivery orders.']],
                ], 422);
            }
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($insuranceRequest, $validated, $user) {
            // Get delivery address if address_id is provided
            $deliveryAddressText = null;
            if (isset($validated['delivery_address_id'])) {
                $deliveryAddress = \App\Models\DeliveryAddress::where('user_id', $user->id)
                    ->find($validated['delivery_address_id']);
                if ($deliveryAddress) {
                    $deliveryAddressText = $deliveryAddress->address;
                }
            } else {
                $deliveryAddressText = $validated['delivery_address'] ?? null;
            }

            // Calculate delivery fee
            $deliveryFee = 0.0;
            if ($validated['delivery_type'] === 'delivery' && isset($validated['delivery_zone_id'])) {
                $deliveryZone = \App\Models\DeliveryZone::find($validated['delivery_zone_id']);
                if ($deliveryZone) {
                    // Cast to float to ensure numeric type
                    $deliveryFee = (float) $deliveryZone->delivery_fee; // Only delivery fee, items are free
                }
            }

            // Create order with only delivery fee
            $order = \App\Models\Order::create([
                'user_id' => $insuranceRequest->user_id,
                'branch_id' => $insuranceRequest->branch_id,
                'delivery_address_id' => $validated['delivery_address_id'] ?? null,
                'delivery_zone_id' => $validated['delivery_zone_id'] ?? null,
                'delivery_type' => $validated['delivery_type'],
                'order_number' => \App\Helpers\OrderHelper::generateOrderNumber(),
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
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product && $product->is_active && !$product->is_expired) {
                        $productId = $product->id;
                    }
                } else {
                    // Try to find product by name for custom products
                    $product = \App\Models\Product::where('name', 'like', "%{$item->product_name}%")
                        ->where('is_active', true)
                        ->where('is_expired', false)
                        ->first();
                    if ($product) {
                        $productId = $product->id;
                    }
                }
                
                // Create order item for all products (including custom ones)
                \App\Models\OrderItem::create([
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

            $order->load(['items.product', 'deliveryZone']);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully. Please proceed to payment.',
                'data' => $order,
            ], 201);
        });
    }
}
