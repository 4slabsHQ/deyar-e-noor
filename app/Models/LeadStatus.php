<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStatus extends Model
{
    protected $fillable = [
        'name', 'slug', 'color', 'sort_order', 'is_won', 'is_lost', 'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_won'     => 'boolean',
        'is_lost'    => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}