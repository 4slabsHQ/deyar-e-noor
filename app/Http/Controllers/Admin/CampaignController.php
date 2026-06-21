<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\Channel;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('channel')->orderBy('name')->paginate(15);
        $channels = Channel::orderBy('name')->get();

        return view('admin.campaigns.index', compact('campaigns', 'channels'));
    }

    public function store(StoreCampaignRequest $request)
    {
        Campaign::create($request->validated());

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign)
    {
        $campaign->update($request->validated());

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->leads()->exists()) {
            return back()->with('error', 'Cannot delete a campaign that has leads linked to it.');
        }

        $campaign->delete();

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign deleted successfully.');
    }
}