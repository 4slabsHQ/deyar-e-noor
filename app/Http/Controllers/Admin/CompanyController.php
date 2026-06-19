<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;

class CompanyController extends Controller
{
    public function __construct(protected CompanyService $service) {}

    public function index()
    {
        $companies = $this->service->getAll();

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        if (Company::exists()) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'Only one company is allowed. Edit the existing company instead.');
        }

        return view('admin.companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        if (Company::exists()) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'Only one company is allowed.');
        }

        $this->service->store($request->validated());

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $this->service->update($company, $request->validated());

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $this->service->delete($company);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}