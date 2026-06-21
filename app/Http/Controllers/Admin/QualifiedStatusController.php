<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQualifiedStatusRequest;
use App\Http\Requests\UpdateQualifiedStatusRequest;
use App\Models\QualifiedStatus;

class QualifiedStatusController extends Controller
{
    public function index()
    {
        $qualifiedStatuses = QualifiedStatus::orderBy('sort_order')->paginate(15);

        return view('admin.qualified-statuses.index', compact('qualifiedStatuses'));
    }

    public function store(StoreQualifiedStatusRequest $request)
    {
        QualifiedStatus::create($request->validated());

        return redirect()->route('admin.qualified-statuses.index')->with('success', 'Qualified status created successfully.');
    }

    public function update(UpdateQualifiedStatusRequest $request, QualifiedStatus $qualified_status)
    {
        $qualified_status->update($request->validated());

        return redirect()->route('admin.qualified-statuses.index')->with('success', 'Qualified status updated successfully.');
    }

    public function destroy(QualifiedStatus $qualified_status)
    {
        if ($qualified_status->leads()->exists()) {
            return back()->with('error', 'Cannot delete a qualified status that has leads linked to it.');
        }

        $qualified_status->delete();

        return redirect()->route('admin.qualified-statuses.index')->with('success', 'Qualified status deleted successfully.');
    }
}