<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubService extends Model
{
    protected $fillable = ['service_id', 'name', 'code', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}