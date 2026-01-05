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

        // Create request items
        foreach ($validated['items'] as $item) {
            InsuranceRequestItem::create([
                'insurance_request_id' => $insuranceRequest->id,
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
        
        $query = InsuranceRequest::with(['insuranceCompany', 'branch', 'items', 'approvedBy']);
        
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
        
        $request = InsuranceRequest::with(['insuranceCompany', 'branch', 'items', 'approvedBy', 'order'])
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
}
