<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Services\SupplierService;

class SupplierController extends Controller
{
    public function __construct(private SupplierService $supplierService)
    {
    }

    public function index()
    {
        $suppliers = Supplier::with(['category', 'country', 'city'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create', $this->formData());
    }

    public function store(StoreSupplierRequest $request)
    {
        $this->supplierService->create($request->validated());

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', $this->formData() + compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $this->supplierService->update($supplier, $request->validated());

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Shared dropdown data for create & edit forms.
     */
    private function formData(): array
    {
        return [
            'categories' => SupplierCategory::orderBy('name')->get(),
            'countries'  => Country::orderBy('name')->get(),
            'cities'     => City::orderBy('name')->get(),
        ];
    }
}