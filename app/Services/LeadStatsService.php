<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Collection;

class LeadStatsService
{
    /**
     * Everything the dashboard view needs, in one call.
     */
    public function summary(): array
    {
        return [
            'total'            => Lead::count(),
            'open'             => Lead::open()->count(),
            'won'              => Lead::won()->count(),
            'lost'             => Lead::lost()->count(),
            'conversion_rate'  => $this->conversionRate(),
            'overdue'          => Lead::overdueFollowUps()->count(),
            'new_this_month'   => Lead::whereMonth('created_at', now()->month)
                                       ->whereYear('created_at', now()->year)
                                       ->count(),
            'by_status'        => $this->countByStatus(),
            'by_channel'       => $this->countByChannel(),
            'recent_leads'     => $this->recentLeads(),
            'todays_follow_ups'=> $this->todaysFollowUps(),
        ];
    }

    public function conversionRate(): float
    {
        $total = Lead::count();

        if ($total === 0) {
            return 0.0;
        }

        return round((Lead::won()->count() / $total) * 100, 1);
    }

    public function countByStatus(): Collection
    {
        return Lead::with('status')
            ->get()
            ->groupBy(fn ($lead) => $lead->status?->name ?? 'Unassigned')
            ->map(fn ($group) => $group->count());
    }

    public function countByChannel(): Collection
    {
        return Lead::with('channel')
            ->get()
            ->groupBy(fn ($lead) => $lead->channel?->name ?? 'Unknown')
            ->map(fn ($group) => $group->count());
    }

    public function recentLeads(int $limit = 10): Collection
    {
        return Lead::with(['status', 'agent'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function todaysFollowUps(): Collection
    {
        return Lead::with(['status', 'agent'])
            ->open()
            ->whereDate('next_follow_up_at', today())
            ->get();
    }
}