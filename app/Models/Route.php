<?php

namespace App\Models;

use App\Concerns\BelongsToHajjSeason;
use App\Concerns\GuardsDeletionWhenReferenced;
use Database\Factories\RouteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    /** @use HasFactory<RouteFactory> */
    use BelongsToHajjSeason, GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'hajj_year',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hajj_year' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RouteStep::class)->orderBy('sequence');
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
        return 'route';
    }

    public function summary(): string
    {
        return $this->relationLoaded('steps')
            ? $this->steps->map(fn (RouteStep $step): string => $step->label())->implode(' → ')
            : '';
    }

    public function registrationOptionLabel(): string
    {
        $summary = $this->summary();

        return $summary !== ''
            ? sprintf('%s — %s', $this->name, $summary)
            : $this->name;
    }
}
