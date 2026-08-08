<?php

namespace App\Services;

use App\Enums\FlightType;
use App\Models\Airline;
use Carbon\Carbon;
use InvalidArgumentException;

class FlightService
{
    public function airlineCode(Airline $airline): string
    {
        $code = strtoupper(trim((string) ($airline->iata_code ?: $airline->code)));

        if ($code === '') {
            throw new InvalidArgumentException('Selected airline has no code configured.');
        }

        return $code;
    }

    public function buildFlightNumber(Airline $airline, string $numberPart): string
    {
        $code = $this->airlineCode($airline);
        $number = strtoupper(preg_replace('/[^A-Z0-9]/', '', $numberPart) ?? '');

        if ($number !== '' && str_starts_with($number, $code)) {
            $number = substr($number, strlen($code));
        }

        return $code.$number;
    }

    public function flightNumberPart(Airline $airline, string $fullFlightNumber): string
    {
        $code = $this->airlineCode($airline);
        $full = strtoupper(trim($fullFlightNumber));

        if (str_starts_with($full, $code)) {
            return substr($full, strlen($code));
        }

        return $full;
    }

    public function calculateStayMinutes(
        string $arrivalDate,
        string $arrivalTime,
        string $departureDate,
        string $departureTime,
    ): int {
        $arrival = Carbon::parse($arrivalDate.' '.$arrivalTime);
        $departure = Carbon::parse($departureDate.' '.$departureTime);

        if ($departure->lessThanOrEqualTo($arrival)) {
            throw new InvalidArgumentException('Via departure must be after via arrival.');
        }

        return (int) $arrival->diffInMinutes($departure);
    }

    public function formatStayDuration(?int $minutes): ?string
    {
        if ($minutes === null || $minutes <= 0) {
            return null;
        }

        $days = intdiv($minutes, 60 * 24);
        $minutes %= 60 * 24;
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        $parts = [];

        if ($days > 0) {
            $parts[] = $days.'d';
        }

        if ($hours > 0) {
            $parts[] = $hours.'h';
        }

        if ($mins > 0 || $parts === []) {
            $parts[] = $mins.'m';
        }

        return implode(' ', $parts);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareFlightData(array $data): array
    {
        $departureAirline = Airline::query()->findOrFail($data['departure_airline_id']);
        $data['departure_flight_no'] = $this->buildFlightNumber(
            $departureAirline,
            (string) $data['departure_flight_number']
        );

        if (($data['flight_type'] ?? null) === FlightType::Indirect->value) {
            $viaAirline = Airline::query()->findOrFail($data['via_airline_id']);
            $data['via_departure_flight_no'] = $this->buildFlightNumber(
                $viaAirline,
                (string) $data['via_departure_flight_number']
            );

            $data['via_total_stay_minutes'] = $this->calculateStayMinutes(
                (string) $data['via_arrival_date'],
                (string) $data['via_arrival_time'],
                (string) $data['via_departure_date'],
                (string) $data['via_departure_time'],
            );
        } else {
            $data['via_city_id'] = null;
            $data['via_airport_id'] = null;
            $data['via_arrival_date'] = null;
            $data['via_arrival_time'] = null;
            $data['via_airline_id'] = null;
            $data['via_departure_flight_no'] = null;
            $data['via_departure_date'] = null;
            $data['via_departure_time'] = null;
            $data['via_total_stay_minutes'] = null;
        }

        unset($data['departure_flight_number'], $data['via_departure_flight_number']);

        return $data;
    }
}
