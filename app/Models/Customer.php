<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'email', 'phone', 'whatsapp',
        'passport_number', 'cnic', 'dob', 'gender', 'address',
        'country_id', 'city_id', 'nationality',
        'customer_type', 'company_name', 'tax_number',
        'credit_limit', 'credit_days', 'is_active', 'notes',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'dob'          => 'date',
        'credit_limit' => 'decimal:2',
        'credit_days'  => 'integer',
        'is_active'    => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}