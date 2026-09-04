<?php

namespace App\Models;

use App\Enums\AccommodationPlanSlot as AccommodationSlotType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccommodationPlanSlot extends Model
{
    protected $fillable = [
        'accommodation_plan_id',
        'slot',
        'property_id',
        'property_akad_id',
        'sequence',
    ];

    protected function casts(): array
    {
        return [
            'slot' => AccommodationSlotType::class,
            'sequence' => 'integer',
        ];
    }

    public function accommodationPlan(): BelongsTo
    {
        return $this->belongsTo(AccommodationPlan::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function akad(): BelongsTo
    {
        return $this->belongsTo(PropertyAkad::class, 'property_akad_id');
    }

    public function displayLabel(): string
    {
        $propertyLabel = $this->property?->registrationOptionLabel() ?? '—';

        if ($this->akad) {
            return sprintf('%s — %s', $propertyLabel, $this->akad->optionLabel());
        }

        return $propertyLabel;
    }
}
