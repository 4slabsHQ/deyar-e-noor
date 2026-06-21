<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadStatusRequest;
use App\Http\Requests\UpdateLeadStatusRequest;
use App\Models\LeadStatus;

class LeadStatusController extends Controller
{
    public function index()
    {
        $leadStatuses = LeadStatus::ordered()->paginate(15);

        return view('admin.lead-statuses.index', compact('leadStatuses'));
    }

    public function store(StoreLeadStatusRequest $request)
    {
        LeadStatus::create($request->validated());

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Lead status created successfully.');
    }

    public function update(UpdateLeadStatusRequest $request, LeadStatus $lead_status)
    {
        $lead_status->update($request->validated());

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Lead status updated successfully.');
    }

    public function destroy(LeadStatus $lead_status)
    {
        if ($lead_status->leads()->exists()) {
            return back()->with('error', 'Cannot delete a status that has leads linked to it.');
        }

        $lead_status->delete();

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Lead status deleted successfully.');
    }
}