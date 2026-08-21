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
        return view('admin.companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        $this->service->store($data);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        $this->service->update($company, $data);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        if ($message = $company->deletionBlockedMessage()) {
            return back()->with('error', $message);
        }

        $this->service->delete($company);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
