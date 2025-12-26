<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Prescription::with(['user', 'approvedBy', 'branch']);

        // If branch staff, filter by their branch
        if ($user->isBranchStaff()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('branch_id') && $request->branch_id !== '' && !$user->isBranchStaff()) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('prescription_number', 'like', "%{$search}%")
                  ->orWhere('doctor_name', 'like', "%{$search}%")
                  ->orWhere('patient_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $prescriptions = $query->latest()->paginate(20);
        $statuses = ['pending', 'approved', 'rejected'];
        
        // Get branches for filter (only if admin)
        $branches = $user->isBranchStaff() ? null : \App\Models\Branch::active()->get();

        return view('admin.prescriptions.index', compact('prescriptions', 'statuses', 'branches'));
    }

    public function show(Prescription $prescription)
    {
        // Check if branch staff can only see their branch prescriptions
        $user = Auth::user();
        if ($user->isBranchStaff() && $prescription->branch_id !== $user->branch_id) {
            abort(403, 'You can only view prescriptions for your branch.');
        }
        
        $prescription->load(['user', 'approvedBy', 'branch', 'orders']);
        return view('admin.prescriptions.show', compact('prescription'));
    }

    public function approve(Request $request, Prescription $prescription)
    {
        if ($prescription->status !== 'pending') {
            return back()->with('error', 'Only pending prescriptions can be approved.');
        }

        $prescription->approve(Auth::id(), $request->notes);

        return back()->with('success', 'Prescription approved successfully.');
    }

    public function reject(Request $request, Prescription $prescription)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($prescription->status !== 'pending') {
            return back()->with('error', 'Only pending prescriptions can be rejected.');
        }

        $prescription->reject(Auth::id(), $request->rejection_reason);

        return back()->with('success', 'Prescription rejected.');
    }

    public function download(Prescription $prescription)
    {
        if (!Storage::disk('public')->exists($prescription->file_path)) {
            abort(404, 'Prescription file not found.');
        }

        return Storage::disk('public')->download($prescription->file_path);
    }
}
