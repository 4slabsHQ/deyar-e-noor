<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of taxes.
     */
    public function index()
    {
        $taxes = Tax::orderBy('name')->paginate(15);

        return view('admin.taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new tax.
     */
    public function create()
    {
        return view('admin.taxes.create');
    }

    /**
     * Store a newly created tax in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateTax($request);
        $validated['created_by'] = auth()->id();

        Tax::create($validated);

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    /**
     * Show the form for editing the specified tax.
     */
    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', compact('tax'));
    }

    /**
     * Update the specified tax in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $validated = $this->validateTax($request, $tax);
        $validated['updated_by'] = auth()->id();

        $tax->update($validated);

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    /**
     * Remove the specified tax from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }

    /**
     * Validate tax input directly in the controller.
     * Shared by store() and update() to avoid repeating rules.
     */
    private function validateTax(Request $request, ?Tax $tax = null): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:255', 'unique:taxes,code,' . ($tax?->id)],
            'rate'       => ['required', 'numeric', 'min:0'],
            'type'       => ['required', 'in:percentage,fixed'],
            'is_active'  => ['boolean'],
        ]);
    }
}