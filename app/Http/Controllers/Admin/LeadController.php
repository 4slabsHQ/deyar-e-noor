<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignLeadRequest;
use App\Http\Requests\ChangeLeadStatusRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Branch;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\City;
use App\Models\Country;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\QualifiedStatus;
use App\Models\Service;
use App\Models\SubService;
use App\Models\User;
use App\Services\LeadService;
use App\Services\LeadStatsService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(
        private LeadService $leadService,
        private LeadStatsService $statsService,
    ) {
    }

    /**
     * The CRM dashboard: pipeline counts, conversion rate, follow-ups due, recent leads.
     */
    public function dashboard()
    {
        $stats = $this->statsService->summary();

        return view('admin.leads.dashboard', compact('stats'));
    }

    /**
     * Filterable list of leads.
     */
    public function index(Request $request)
    {
        $leads = Lead::with(['status', 'qualifiedStatus', 'channel', 'agent'])
            ->when($request->filled('status'), fn ($q) => $q->where('lead_status_id', $request->status))
            ->when($request->filled('assigned_to'), fn ($q) => $q->assignedTo($request->assigned_to))
            ->when($request->filled('channel_id'), fn ($q) => $q->where('channel_id', $request->channel_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(fn ($q) => $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('lead_no', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads'    => $leads,
            'statuses' => LeadStatus::ordered()->get(),
            'channels' => Channel::orderBy('name')->get(),
            'agents'   => User::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.leads.create', $this->formData());
    }

    public function store(StoreLeadRequest $request)
    {
        $this->leadService->create($request->validated());

        return redirect()->route('admin.leads.index')->with('success', 'Lead created successfully.');
    }

    public function edit(Lead $lead)
    {
        $lead->load('activities.performer');

        return view('admin.leads.edit', $this->formData() + compact('lead'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $this->leadService->update($lead, $request->validated());

        return redirect()->route('admin.leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->leadService->delete($lead);

        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function assign(AssignLeadRequest $request, Lead $lead)
    {
        $this->leadService->assign($lead, $request->validated('assigned_to'));

        return back()->with('success', 'Lead reassigned successfully.');
    }

    public function changeStatus(ChangeLeadStatusRequest $request, Lead $lead)
    {
        $this->leadService->changeStatus(
            $lead,
            $request->validated('lead_status_id'),
            $request->validated('reason')
        );

        return back()->with('success', 'Lead status updated successfully.');
    }

    public function convert(Lead $lead)
    {
        $customer = $this->leadService->convertToCustomer($lead);

        return redirect()
            ->route('admin.customers.edit', $customer)
            ->with('success', 'Lead converted to customer successfully.');
    }

    /**
     * Shared dropdown data for create & edit forms.
     */
    private function formData(): array
    {
        return [
            'countries'         => Country::orderBy('name')->get(),
            'cities'            => City::orderBy('name')->get(),
            'branches'          => Branch::orderBy('name')->get(),
            'services'          => Service::orderBy('name')->get(),
            'subServices'       => SubService::orderBy('name')->get(),
            'channels'          => Channel::orderBy('name')->get(),
            'campaigns'         => Campaign::orderBy('name')->get(),
            'statuses'          => LeadStatus::ordered()->get(),
            'qualifiedStatuses' => QualifiedStatus::orderBy('sort_order')->get(),
            'agents'            => User::orderBy('name')->get(),
        ];
    }
}