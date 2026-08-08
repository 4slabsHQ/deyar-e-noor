<?php

namespace App\Models;

use App\Enums\FlightType;
use App\Services\FlightService;
use Database\Factories\FlightFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    /** @use HasFactory<FlightFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_type',
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

    public function getViaTotalStayLabelAttribute(): ?string
    {
        if ($this->via_total_stay_minutes === null) {
            return null;
        }

        return app(FlightService::class)->formatStayDuration($this->via_total_stay_minutes);
    }
}
