<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCompany;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function insurance()
    {
        $insuranceCompanies = InsuranceCompany::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::active()->get();
        return view('web.user.services.insurance', compact('insuranceCompanies', 'branches'));
    }

    public function storeInsurance(Request $request)
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
        $requestNumber = \App\Models\InsuranceRequest::generateRequestNumber();

        // Create insurance request
        $insuranceRequest = \App\Models\InsuranceRequest::create([
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

        // Create request items
        foreach ($validated['items'] as $item) {
            \App\Models\InsuranceRequestItem::create([
                'insurance_request_id' => $insuranceRequest->id,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Insurance request submitted successfully. A pharmacist will review your request and contact you soon.');
    }

    public function prescription()
    {
        $branches = Branch::active()->get();
        return view('web.user.services.prescription', compact('branches'));
    }

    public function storePrescription(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'doctor_name' => 'nullable|string|max:255',
            'patient_name' => 'required|string|max:255',
            'prescription_date' => 'nullable|date',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'prescription_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Handle image upload
        if ($request->hasFile('prescription_image')) {
            $image = $request->file('prescription_image');
            $path = $image->store('prescriptions', 'public');
            $validated['file_path'] = $path;
        }

        // Generate prescription number
        $validated['prescription_number'] = 'PRES-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        
        // Set user_id
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        \App\Models\Prescription::create($validated);

        return redirect()->route('user.dashboard')
            ->with('success', 'Prescription submitted successfully. A pharmacist will call you soon.');
    }

    public function itemRequest()
    {
        $branches = Branch::active()->get();
        return view('web.user.services.item-request', compact('branches'));
    }

    public function storeItemRequest(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        // Generate request number
        $validated['request_number'] = \App\Models\ItemRequest::generateRequestNumber();
        
        // Set user_id
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['quantity'] = $validated['quantity'] ?? 1;

        \App\Models\ItemRequest::create($validated);

        return redirect()->route('user.dashboard')
            ->with('success', 'Item request submitted successfully. We will notify you when the item is available.');
    }

    public function insuranceRequests()
    {
        $requests = \App\Models\InsuranceRequest::where('user_id', Auth::id())
            ->with(['insuranceCompany', 'branch', 'items', 'approvedBy', 'order'])
            ->latest()
            ->paginate(10);

        return view('web.user.services.insurance-requests', compact('requests'));
    }

    public function showInsuranceRequest($id)
    {
        $request = \App\Models\InsuranceRequest::where('user_id', Auth::id())
            ->with(['insuranceCompany', 'branch', 'items', 'approvedBy', 'order', 'deliveryZone', 'deliveryAddress'])
            ->findOrFail($id);

        return view('web.user.services.insurance-request-show', compact('request'));
    }

    public function insuranceRequestOrder($id)
    {
        $request = \App\Models\InsuranceRequest::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->with(['insuranceCompany', 'branch', 'items'])
            ->findOrFail($id);

        if ($request->order_id) {
            return redirect()->route('user.services.insurance-requests.show', $request->id)
                ->with('info', 'Order already created for this insurance request.');
        }

        $deliveryZones = \App\Models\DeliveryZone::all();
        $addresses = \App\Models\DeliveryAddress::where('user_id', Auth::id())->get();

        return view('web.user.services.insurance-request-order', compact('request', 'deliveryZones', 'addresses'));
    }

    public function storeInsuranceRequestOrder(Request $request, $id)
    {
        $insuranceRequest = \App\Models\InsuranceRequest::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->findOrFail($id);

        if ($insuranceRequest->order_id) {
            return redirect()->route('user.services.insurance-requests.show', $insuranceRequest->id)
                ->with('info', 'Order already created for this insurance request.');
        }

        $validated = $request->validate([
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address_id' => 'nullable|integer|exists:delivery_addresses,id',
            'delivery_zone_id' => 'nullable|integer|exists:delivery_zones,id',
            'delivery_address' => 'nullable|string',
        ]);

        // Additional validation for delivery type
        if ($validated['delivery_type'] === 'delivery') {
            if (empty($validated['delivery_zone_id'])) {
                return back()->withErrors(['delivery_zone_id' => 'Delivery zone is required for delivery orders.'])->withInput();
            }
            
            // Verify delivery zone exists
            $deliveryZone = \App\Models\DeliveryZone::find($validated['delivery_zone_id']);
            if (!$deliveryZone) {
                return back()->withErrors(['delivery_zone_id' => 'Invalid delivery zone selected.'])->withInput();
            }
            
            // Either delivery_address_id or delivery_address must be provided
            if (empty($validated['delivery_address_id']) && empty($validated['delivery_address'])) {
                return back()->withErrors(['delivery_address' => 'Please select a saved address or enter a delivery address.'])->withInput();
            }
            
            // If delivery_address_id is provided, verify it belongs to the user
            if (!empty($validated['delivery_address_id'])) {
                $deliveryAddress = \App\Models\DeliveryAddress::where('user_id', Auth::id())
                    ->find($validated['delivery_address_id']);
                if (!$deliveryAddress) {
                    return back()->withErrors(['delivery_address_id' => 'Selected delivery address does not belong to you.'])->withInput();
                }
            }
        }

        // Get delivery address if address_id is provided
        $deliveryAddressText = null;
        if (isset($validated['delivery_address_id']) && !empty($validated['delivery_address_id'])) {
            $deliveryAddress = \App\Models\DeliveryAddress::where('user_id', Auth::id())
                ->find($validated['delivery_address_id']);
            if ($deliveryAddress) {
                $deliveryAddressText = $deliveryAddress->address;
            }
        } else {
            $deliveryAddressText = $validated['delivery_address'] ?? null;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($insuranceRequest, $validated, $deliveryAddressText) {
            // Calculate delivery fee
            $deliveryFee = 0.0;
            if ($validated['delivery_type'] === 'delivery' && isset($validated['delivery_zone_id'])) {
                $deliveryZone = \App\Models\DeliveryZone::find($validated['delivery_zone_id']);
                if ($deliveryZone) {
                    // Cast to float to ensure numeric type
                    $deliveryFee = (float) $deliveryZone->delivery_fee;
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
                'subtotal' => 0.0,
                'delivery_fee' => $deliveryFee,
                'discount' => 0.0,
                'total_amount' => $deliveryFee,
            ]);

            // Create order items
            foreach ($insuranceRequest->items as $item) {
                $product = \App\Models\Product::where('name', 'like', "%{$item->product_name}%")
                    ->where('is_active', true)
                    ->where('is_expired', false)
                    ->first();
                
                if ($product) {
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'unit_price' => 0,
                        'total_price' => 0,
                    ]);
                }
            }

            // Update insurance request
            $insuranceRequest->update([
                'status' => 'order_created',
                'order_id' => $order->id,
                'delivery_address_id' => $validated['delivery_address_id'] ?? null,
                'delivery_zone_id' => $validated['delivery_zone_id'] ?? null,
                'delivery_type' => $validated['delivery_type'],
            ]);

            // If pickup or total amount is 0, skip payment and go to success
            if ($validated['delivery_type'] === 'pickup' || $order->total_amount == 0) {
                return redirect()->route('user.services.insurance-requests')
                    ->with('success', 'Order created successfully! You can collect it from the branch.');
            }

            return redirect()->route('checkout.payment', $order)
                ->with('success', 'Order created successfully. Please proceed to payment.');
        });
    }
}
