<?php

namespace Database\Factories;

use App\Enums\FlightDirection;
use App\Enums\FlightType;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\City;
use App\Models\Country;
use App\Models\Flight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Flight>
 */
class FlightFactory extends Factory
{
    protected $model = Flight::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::factory()->create();
        $departureCity = City::factory()->create(['country_id' => $country->id, 'is_active' => true]);
        $arrivalCity = City::factory()->create(['country_id' => $country->id, 'is_active' => true]);
        $departureAirport = Airport::factory()->create(['city_id' => $departureCity->id, 'is_active' => true]);
        $arrivalAirport = Airport::factory()->create(['city_id' => $arrivalCity->id, 'is_active' => true]);
        $airline = Airline::query()->create([
            'name' => 'Test Airline',
            'code' => strtoupper(fake()->unique()->bothify('??###')),
            'iata_code' => strtoupper(fake()->lexify('??')),
            'country_id' => $country->id,
            'is_active' => true,
        ]);

        $departureDate = fake()->dateTimeBetween('+1 week', '+2 months');

        return [
            'flight_type' => FlightType::Direct,
            'direction' => FlightDirection::Outbound,
            'departure_city_id' => $departureCity->id,
            'departure_airport_id' => $departureAirport->id,
            'departure_airline_id' => $airline->id,
            'departure_flight_no' => 'TA'.fake()->numerify('###'),
            'departure_date' => $departureDate->format('Y-m-d'),
            'departure_time' => '08:30:00',
            'arrival_city_id' => $arrivalCity->id,
            'arrival_airport_id' => $arrivalAirport->id,
            'arrival_date' => $departureDate->format('Y-m-d'),
            'arrival_time' => '14:45:00',
        ];
    }

    public function indirect(): static
    {
        return $this->state(function (array $attributes) {
            $country = Country::factory()->create();
            $viaCity = City::factory()->create(['country_id' => $country->id, 'is_active' => true]);
            $viaAirport = Airport::factory()->create(['city_id' => $viaCity->id, 'is_active' => true]);
            $viaAirline = Airline::query()->create([
                'name' => 'Via Airline',
                'code' => strtoupper(fake()->unique()->bothify('??###')),
                'iata_code' => strtoupper(fake()->lexify('??')),
                'country_id' => $country->id,
                'is_active' => true,
            ]);

            return [
                'flight_type' => FlightType::Indirect,
                'via_city_id' => $viaCity->id,
                'via_airport_id' => $viaAirport->id,
                'via_arrival_date' => $attributes['departure_date'],
                'via_arrival_time' => '11:00:00',
                'via_airline_id' => $viaAirline->id,
                'via_departure_flight_no' => 'VA456',
                'via_departure_date' => $attributes['departure_date'],
                'via_departure_time' => '16:30:00',
                'via_total_stay_minutes' => 330,
            ];
        });
    }
}
