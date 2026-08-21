<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airline extends Model
{
    use GuardsDeletionWhenReferenced, SoftDeletes;

    protected $fillable = [
        'name', 'code', 'iata_code', 'icao_code', 'logo',
        'country_id', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** @return array<int, callable(Airline): ?string> */
    public function referencedByChecks(): array
    {
        return [
            function (Airline $airline): ?string {
                $count = Flight::query()->usingAirline($airline->id)->count();

                if ($count === 0) {
                    return null;
                }

                return sprintf(
                    'Cannot delete this airline because it is linked to %d flight(s).',
                    $count,
                );
            },
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'airline';
    }
}
