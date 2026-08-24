<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use App\Enums\FlightDirection;
use App\Enums\FlightType;
use App\Services\FlightService;
use Database\Factories\FlightFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    /** @use HasFactory<FlightFactory> */
    use GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_type',
        'direction',
        'departure_city_id',
        'departure_airport_id',
        'departure_airline_id',
        'departure_flight_no',
        'departure_date',
        'departure_time',
        'via_city_id',
        'via_airport_id',
        'via_arrival_date',
        'via_arrival_time',
        'via_airline_id',
        'via_departure_flight_no',
        'via_departure_date',
        'via_departure_time',
        'via_total_stay_minutes',
        'arrival_city_id',
        'arrival_airport_id',
        'arrival_date',
        'arrival_time',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'flight_type' => FlightType::class,
            'direction' => FlightDirection::class,
            'departure_date' => 'date',
            'via_arrival_date' => 'date',
            'via_departure_date' => 'date',
            'arrival_date' => 'date',
            'via_total_stay_minutes' => 'integer',
        ];
    }

    public function departureCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'departure_city_id');
    }

    public function departureAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function departureAirline(): BelongsTo
    {
        return $this->belongsTo(Airline::class, 'departure_airline_id');
    }

    public function viaCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'via_city_id');
    }

    public function viaAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'via_airport_id');
    }

    public function viaAirline(): BelongsTo
    {
        return $this->belongsTo(Airline::class, 'via_airline_id');
    }

    public function arrivalCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'arrival_city_id');
    }

    public function arrivalAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function pilgrims(): BelongsToMany
    {
        return $this->belongsToMany(Pilgrim::class)
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    /** @return array<string, string> */
    public function referencedByRelations(): array
    {
        return [
            'pilgrims' => 'Hajj registrations',
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'flight';
    }

    /** @param  Builder<Flight>  $query */
    public function scopeUsingCity(Builder $query, int $cityId): Builder
    {
        return $query->where(function (Builder $query) use ($cityId): void {
            $query->where('departure_city_id', $cityId)
                ->orWhere('via_city_id', $cityId)
                ->orWhere('arrival_city_id', $cityId);
        });
    }

    /** @param  Builder<Flight>  $query */
    public function scopeUsingAirport(Builder $query, int $airportId): Builder
    {
        return $query->where(function (Builder $query) use ($airportId): void {
            $query->where('departure_airport_id', $airportId)
                ->orWhere('via_airport_id', $airportId)
                ->orWhere('arrival_airport_id', $airportId);
        });
    }

    /** @param  Builder<Flight>  $query */
    public function scopeUsingAirline(Builder $query, int $airlineId): Builder
    {
        return $query->where(function (Builder $query) use ($airlineId): void {
            $query->where('departure_airline_id', $airlineId)
                ->orWhere('via_airline_id', $airlineId);
        });
    }

    public function getViaTotalStayLabelAttribute(): ?string
    {
        if ($this->via_total_stay_minutes === null) {
            return null;
        }

        return app(FlightService::class)->formatStayDuration($this->via_total_stay_minutes);
    }

    public function reportFilterLabel(): string
    {
        $date = $this->departure_date?->format('d M Y') ?? 'No date';

        return sprintf(
            '%s — %s — %s',
            $this->direction->label(),
            $this->departure_flight_no,
            $date,
        );
    }
}
