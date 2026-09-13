<?php

namespace App\Models;

use App\Enums\AccommodationPlanSlot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilgrimAccommodationSlot extends Model
{
    protected $fillable = [
        'pilgrim_id',
        'slot',
        'property_akad_id',
        'room_number',
    ];

    protected function casts(): array
    {
        return [
            'slot' => AccommodationPlanSlot::class,
        ];
    }

    public function pilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function akad(): BelongsTo
    {
        return $this->belongsTo(PropertyAkad::class, 'property_akad_id');
    }
}
