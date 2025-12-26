<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrescriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = Prescription::with(['branch', 'approvedBy']);
        
        if ($user) {
            $query->where('user_id', $user->id);
        }
        
        $prescriptions = $query->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $prescriptions,
        ]);
    }

    public function store(Request $request)
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
        
        // Set user_id if authenticated, otherwise null for guest
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        $prescription = Prescription::create($validated);
        $prescription->load(['branch', 'approvedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Prescription submitted successfully. A pharmacist will call you soon.',
            'data' => $prescription,
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $prescription = Prescription::with(['branch', 'approvedBy', 'user'])
            ->where(function($query) use ($user) {
                if ($user) {
                    $query->where('user_id', $user->id);
                }
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $prescription,
        ]);
    }
}
