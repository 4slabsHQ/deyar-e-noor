<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyAkad extends Model
{
    protected $fillable = [
        'property_id',
        'akad_number',
        'label',
        'notes',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function planSlots(): HasMany
    {
        return $this->hasMany(AccommodationPlanSlot::class);
    }

    public function optionLabel(): string
    {
        return $this->label
            ? sprintf('%s — %s', $this->akad_number, $this->label)
            : $this->akad_number;
    }
}
