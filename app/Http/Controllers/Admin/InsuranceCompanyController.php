<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;

class InsuranceCompanyController extends Controller
{
    public function index()
    {
        $companies = InsuranceCompany::orderBy('name')->paginate(20);
        return view('admin.insurance-companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.insurance-companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:insurance_companies,name',
        ]);

        $validated['is_active'] = $request->has('is_active');

        InsuranceCompany::create($validated);

        return redirect()->route('admin.insurance-companies.index')
            ->with('success', 'Insurance company created successfully.');
    }

    public function show($id)
    {
        $company = InsuranceCompany::with('insuranceRequests')->findOrFail($id);
        return view('admin.insurance-companies.show', compact('company'));
    }

    public function edit($id)
    {
        $company = InsuranceCompany::findOrFail($id);
        return view('admin.insurance-companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = InsuranceCompany::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:insurance_companies,name,' . $id,
        ]);

        $validated['is_active'] = $request->has('is_active');

        $company->update($validated);

        return redirect()->route('admin.insurance-companies.index')
            ->with('success', 'Insurance company updated successfully.');
    }

    public function destroy($id)
    {
        $company = InsuranceCompany::findOrFail($id);
        
        // Check if company has requests
        if ($company->insuranceRequests()->count() > 0) {
            return redirect()->route('admin.insurance-companies.index')
                ->with('error', 'Cannot delete insurance company that has requests. Please deactivate it instead.');
        }

        $company->delete();

        return redirect()->route('admin.insurance-companies.index')
            ->with('success', 'Insurance company deleted successfully.');
    }
}
