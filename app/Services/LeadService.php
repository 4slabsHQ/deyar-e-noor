<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadStatus;

class LeadService
{
    /**
     * Create a new lead. Defaults to the first open pipeline status
     * if none was supplied, and logs the creation as the first activity.
     */
    public function create(array $data): Lead
    {
        $data['lead_no'] = $this->generateLeadNumber();
        $data['lead_status_id'] ??= $this->defaultStatusId();
        $data['created_by'] = auth()->id();

        $lead = Lead::create($data);

        $this->logActivity($lead, 'note', 'Lead created');

        return $lead;
    }

    /**
     * Update a lead. If the pipeline status changed via this form,
     * route it through changeStatus() so it gets logged consistently.
     */
    public function update(Lead $lead, array $data): Lead
    {
        $newStatusId = $data['lead_status_id'] ?? $lead->lead_status_id;
        unset($data['lead_status_id']);

        $data['updated_by'] = auth()->id();
        $lead->update($data);

        if ($newStatusId !== $lead->lead_status_id) {
            $this->changeStatus($lead, $newStatusId);
        }

        return $lead->refresh();
    }

    public function delete(Lead $lead): void
    {
        $lead->delete();
    }

    /**
     * Reassign a lead to a different sales agent.
     */
    public function assign(Lead $lead, int $userId): Lead
    {
        $lead->update(['assigned_to' => $userId]);

        $this->logActivity($lead, 'note', 'Lead reassigned', "Assigned to user #{$userId}");

        return $lead;
    }

    /**
     * Move a lead through the pipeline. Stamps converted_at when it lands
     * on a "won" status and stores the reason when it lands on "lost".
     */
    public function changeStatus(Lead $lead, int $statusId, ?string $reason = null): Lead
    {
        $oldStatus = $lead->status;
        $newStatus = LeadStatus::findOrFail($statusId);

        $lead->lead_status_id = $newStatus->id;
        $lead->lost_reason = $newStatus->is_lost ? $reason : null;

        if ($newStatus->is_won && ! $lead->converted_at) {
            $lead->converted_at = now();
        }

        $lead->last_activity_at = now();
        $lead->save();

        $this->logActivity(
            $lead,
            'status_change',
            'Status changed',
            sprintf('From "%s" to "%s"', $oldStatus?->name ?? 'None', $newStatus->name)
        );

        return $lead;
    }

    /**
     * Log a timeline entry against a lead (call, email, meeting, note, etc.)
     * and bump last_activity_at / next_follow_up_at accordingly.
     */
    public function addActivity(Lead $lead, array $data): LeadActivity
    {
        $data['lead_id'] = $lead->id;
        $data['performed_by'] = auth()->id();

        $activity = LeadActivity::create($data);

        $lead->last_activity_at = now();

        if (! empty($data['due_at']) && empty($data['completed_at'])) {
            $lead->next_follow_up_at = $data['due_at'];
        }

        $lead->save();

        return $activity;
    }

    /**
     * Turn a won lead into a real customer record, linking the two together.
     */
    public function convertToCustomer(Lead $lead): Customer
    {
        if ($lead->customer_id) {
            return $lead->customer;
        }

        $customer = Customer::create([
            'name'          => $lead->full_name,
            'email'         => $lead->email,
            'phone'         => $lead->phone,
            'whatsapp'      => $lead->whatsapp,
            'country_id'    => $lead->country_id,
            'city_id'       => $lead->city_id,
            'customer_type' => 'individual',
            'created_by'    => auth()->id(),
        ]);

        $lead->update([
            'customer_id'   => $customer->id,
            'converted_at'  => $lead->converted_at ?? now(),
        ]);

        $this->logActivity($lead, 'note', 'Converted to customer', "Customer #{$customer->id} created");

        return $customer;
    }

    private function logActivity(Lead $lead, string $type, string $subject, ?string $description = null): void
    {
        LeadActivity::create([
            'lead_id'      => $lead->id,
            'activity_type' => $type,
            'subject'      => $subject,
            'description'  => $description,
            'performed_by' => auth()->id(),
        ]);
    }

    private function defaultStatusId(): ?int
    {
        return LeadStatus::ordered()->where('is_won', false)->where('is_lost', false)->value('id');
    }

    /**
     * LEAD-{year}-{sequence}, e.g. LEAD-2026-00001.
     */
    private function generateLeadNumber(): string
    {
        $year = now()->format('Y');
        $sequence = Lead::whereYear('created_at', $year)->count() + 1;

        return sprintf('LEAD-%s-%05d', $year, $sequence);
    }
}