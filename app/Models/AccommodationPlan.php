<?php

namespace App\Models;

use App\Concerns\BelongsToHajjSeason;
use App\Concerns\GuardsDeletionWhenReferenced;
use App\Enums\AccommodationPlanType;
use Database\Factories\AccommodationPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccommodationPlan extends Model
{
    /** @use HasFactory<AccommodationPlanFactory> */
    use BelongsToHajjSeason, GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'hajj_year',
        'name',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hajj_year' => 'integer',
            'type' => AccommodationPlanType::class,
            'is_active' => 'boolean',
        ];
    }

    public function slots(): HasMany
    {
        return $this->hasMany(AccommodationPlanSlot::class)->orderBy('sequence');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    /** @return array<string, string> */
    public function referencedByRelations(): array
    {
        return [
            'packages' => 'packages',
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'accommodation plan';
    }

    public function registrationOptionLabel(): string
    {
        return sprintf('%s (%s)', $this->name, $this->type->label());
    }
}
