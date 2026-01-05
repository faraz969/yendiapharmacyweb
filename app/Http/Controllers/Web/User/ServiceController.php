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
}
