<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['country', 'city'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create', $this->formData());
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        Customer::create($data);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', $this->formData() + compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $customer->update($data);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Shared dropdown data for create & edit forms.
     */
    private function formData(): array
    {
        return [
            'countries' => Country::orderBy('name')->get(),
            'cities'    => City::orderBy('name')->get(),
        ];
    }
}