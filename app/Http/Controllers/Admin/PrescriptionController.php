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
        $query = Prescription::with(['user', 'approvedBy']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('prescription_number', 'like', "%{$search}%")
                  ->orWhere('doctor_name', 'like', "%{$search}%")
                  ->orWhere('patient_name', 'like', "%{$search}%");
            });
        }

        $prescriptions = $query->latest()->paginate(20);
        $statuses = ['pending', 'approved', 'rejected'];

        return view('admin.prescriptions.index', compact('prescriptions', 'statuses'));
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['user', 'approvedBy', 'orders']);
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
        if ($prescription->status !== 'pending') {
            return back()->with('error', 'Only pending prescriptions can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $prescription->reject(Auth::id(), $request->rejection_reason);

        return back()->with('success', 'Prescription rejected.');
    }

    public function destroy(Prescription $prescription)
    {
        if ($prescription->file_path && Storage::disk('public')->exists($prescription->file_path)) {
            Storage::disk('public')->delete($prescription->file_path);
        }

        $prescription->delete();

        return redirect()->route('admin.prescriptions.index')
            ->with('success', 'Prescription deleted successfully.');
    }
}
