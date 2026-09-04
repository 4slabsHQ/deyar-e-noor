<?php

namespace App\Models;

use App\Concerns\BelongsToHajjSeason;
use App\Concerns\GuardsDeletionWhenReferenced;
use App\Enums\PropertyCity;
use App\Enums\PropertyType;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use BelongsToHajjSeason, GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'hajj_year',
        'name',
        'city',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hajj_year' => 'integer',
            'city' => PropertyCity::class,
            'type' => PropertyType::class,
            'is_active' => 'boolean',
        ];
    }

    public function akads(): HasMany
    {
        return $this->hasMany(PropertyAkad::class)->orderBy('akad_number');
    }

    public function planSlots(): HasMany
    {
        return $this->hasMany(AccommodationPlanSlot::class);
    }

    /** @return array<string, string> */
    public function referencedByRelations(): array
    {
        return [
            'planSlots' => 'accommodation plans',
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'property';
    }

    public function registrationOptionLabel(): string
    {
        return sprintf('%s (%s · %s)', $this->name, $this->city->label(), $this->type->label());
    }
}
