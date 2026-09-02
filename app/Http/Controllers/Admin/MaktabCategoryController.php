<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaktabCategoryRequest;
use App\Http\Requests\UpdateMaktabCategoryRequest;
use App\Models\MaktabCategory;

class MaktabCategoryController extends Controller
{
    public function index()
    {
        $maktabCategories = MaktabCategory::query()->forActiveYear()->orderBy('name')->orderBy('zone')->get();

        return view('admin.maktab-categories.index', compact('maktabCategories'));
    }

    public function create()
    {
        return view('admin.maktab-categories.create');
    }

    public function store(StoreMaktabCategoryRequest $request)
    {
        MaktabCategory::create($this->withActiveHajjYear($request->validated()));

        return redirect()->route('admin.maktab-categories.index')->with('success', 'Maktab category created successfully.');
    }

    public function edit(MaktabCategory $maktabCategory)
    {
        return view('admin.maktab-categories.edit', compact('maktabCategory'));
    }

    public function update(UpdateMaktabCategoryRequest $request, MaktabCategory $maktabCategory)
    {
        $maktabCategory->update($request->validated());

        return redirect()->route('admin.maktab-categories.index')->with('success', 'Maktab category updated successfully.');
    }

    public function destroy(MaktabCategory $maktabCategory)
    {
        return $this->deleteOrBack(
            $maktabCategory,
            'admin.maktab-categories.index',
            'Maktab category deleted successfully.',
        );
    }
}
