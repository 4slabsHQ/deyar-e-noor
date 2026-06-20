<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderBy('name')->paginate(15);

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(StoreCurrencyRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['is_default'])) {
            Currency::query()->update(['is_default' => false]);
        }

        Currency::create($data);

        return redirect()->route('admin.currencies.index')->with('success', 'Currency created successfully.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        $data = $request->validated();

        if (!empty($data['is_default'])) {
            Currency::query()->where('id', '!=', $currency->id)->update(['is_default' => false]);
        }

        $currency->update($data);

        return redirect()->route('admin.currencies.index')->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', 'Cannot delete the default currency.');
        }

        $currency->delete();

        return redirect()->route('admin.currencies.index')->with('success', 'Currency deleted successfully.');
    }
}