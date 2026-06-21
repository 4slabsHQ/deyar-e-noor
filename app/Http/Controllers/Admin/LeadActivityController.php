<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadActivityRequest;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Services\LeadService;

class LeadActivityController extends Controller
{
    public function __construct(private LeadService $leadService)
    {
    }

    public function store(StoreLeadActivityRequest $request, Lead $lead)
    {
        $this->leadService->addActivity($lead, $request->validated());

        return back()->with('success', 'Activity logged successfully.');
    }

    public function destroy(Lead $lead, LeadActivity $activity)
    {
        abort_unless($activity->lead_id === $lead->id, 404);

        $activity->delete();

        return back()->with('success', 'Activity removed.');
    }
}