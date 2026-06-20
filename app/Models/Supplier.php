<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_category_id', 'name', 'code', 'contact_person',
        'email', 'phone', 'whatsapp', 'address',
        'country_id', 'city_id', 'tax_number',
        'bank_name', 'bank_account', 'bank_iban',
        'portal_email', 'portal_password', 'portal_access',
        'is_active', 'notes', 'created_by', 'updated_by',
    ];

    // Never expose the portal password when the model is converted to array/json
    protected $hidden = [
        'portal_password',
    ];

    protected $casts = [
        'portal_access' => 'boolean',
        'is_active'     => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SupplierCategory::class, 'supplier_category_id');
    }

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