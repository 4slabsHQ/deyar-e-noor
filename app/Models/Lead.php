<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lead_no', 'full_name', 'email', 'phone', 'whatsapp',
        'country_id', 'city_id', 'branch_id',
        'service_id', 'sub_service_id',
        'channel_id', 'campaign_id',
        'lead_status_id', 'qualified_status_id', 'assigned_to',
        'customer_id', 'converted_at',
        'priority', 'expected_value', 'expected_close_date',
        'next_follow_up_at', 'last_activity_at', 'lost_reason',
        'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'expected_value'      => 'decimal:2',
        'expected_close_date' => 'date',
        'next_follow_up_at'   => 'datetime',
        'last_activity_at'    => 'datetime',
        'converted_at'        => 'datetime',
        'is_active'           => 'boolean',
    ];

    // ── Relationships ──────────────────────────────

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function subService(): BelongsTo
    {
        return $this->belongsTo(SubService::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    public function qualifiedStatus(): BelongsTo
    {
        return $this->belongsTo(QualifiedStatus::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    // ── Query Scopes (used heavily by the dashboard) ──

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->where('is_won', false)->where('is_lost', false));
    }

    public function scopeWon(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->where('is_won', true));
    }

    public function scopeLost(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->where('is_lost', true));
    }

    public function scopeOverdueFollowUps(Builder $query): Builder
    {
        return $query->open()->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now());
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }
}