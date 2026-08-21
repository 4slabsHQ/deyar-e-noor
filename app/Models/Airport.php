<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use Database\Factories\AirportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airport extends Model
{
    /** @use HasFactory<AirportFactory> */
    use GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'city_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** @return array<int, callable(Airport): ?string> */
    public function referencedByChecks(): array
    {
        return [
            function (Airport $airport): ?string {
                $count = Flight::query()->usingAirport($airport->id)->count();

                if ($count === 0) {
                    return null;
                }

                return sprintf(
                    'Cannot delete this airport because it is linked to %d flight(s).',
                    $count,
                );
            },
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'airport';
    }
}
