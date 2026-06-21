<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['name', 'code', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function subServices(): HasMany
    {
        return $this->hasMany(SubService::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}