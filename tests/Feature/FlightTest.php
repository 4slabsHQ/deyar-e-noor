<?php

use App\Enums\FlightDirection;
use App\Enums\FlightType;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\City;
use App\Models\Country;
use App\Models\Flight;
use App\Models\Pilgrim;
use App\Models\User;
use App\Services\FlightService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');

    $this->country = Country::factory()->create(['iso2' => 'PK', 'name' => 'Pakistan']);
    $this->departureCity = City::factory()->create(['country_id' => $this->country->id, 'name' => 'Lahore', 'is_active' => true]);
    $this->viaCity = City::factory()->create(['country_id' => $this->country->id, 'name' => 'Dubai', 'is_active' => true]);
    $this->arrivalCity = City::factory()->create(['country_id' => $this->country->id, 'name' => 'Jeddah', 'is_active' => true]);

    $this->departureAirport = Airport::factory()->create(['city_id' => $this->departureCity->id, 'code' => 'LHE', 'is_active' => true]);
    $this->viaAirport = Airport::factory()->create(['city_id' => $this->viaCity->id, 'code' => 'DXB', 'is_active' => true]);
    $this->arrivalAirport = Airport::factory()->create(['city_id' => $this->arrivalCity->id, 'code' => 'JED', 'is_active' => true]);

    $this->departureAirline = Airline::query()->create([
        'name' => 'Pakistan International Airlines',
        'code' => 'PK',
        'iata_code' => 'PK',
        'country_id' => $this->country->id,
        'is_active' => true,
    ]);

    $this->viaAirline = Airline::query()->create([
        'name' => 'Emirates',
        'code' => 'EK',
        'iata_code' => 'EK',
        'country_id' => $this->country->id,
        'is_active' => true,
    ]);
});

function validDirectFlightPayload(array $overrides = []): array
{
    return array_merge([
        'flight_type' => FlightType::Direct->value,
        'direction' => FlightDirection::Outbound->value,
        'departure_city_id' => test()->departureCity->id,
        'departure_airport_id' => test()->departureAirport->id,
        'departure_airline_id' => test()->departureAirline->id,
        'departure_flight_number' => '740',
        'departure_date' => now()->addWeek()->toDateString(),
        'departure_time' => '08:30',
        'arrival_city_id' => test()->arrivalCity->id,
        'arrival_airport_id' => test()->arrivalAirport->id,
        'arrival_date' => now()->addWeek()->toDateString(),
        'arrival_time' => '14:45',
    ], $overrides);
}

function validIndirectFlightPayload(array $overrides = []): array
{
    return array_merge(validDirectFlightPayload(), [
        'flight_type' => FlightType::Indirect->value,
        'via_city_id' => test()->viaCity->id,
        'via_airport_id' => test()->viaAirport->id,
        'via_arrival_date' => now()->addWeek()->toDateString(),
        'via_arrival_time' => '11:00',
        'via_airline_id' => test()->viaAirline->id,
        'via_departure_flight_number' => '512',
        'via_departure_date' => now()->addWeek()->toDateString(),
        'via_departure_time' => '16:30',
    ], $overrides);
}

it('lists flights for authorized users', function () {
    Flight::factory()->count(2)->create();

    $this->actingAs($this->user)
        ->get(route('admin.flights.index'))
        ->assertOk()
        ->assertSee('Flights');
});

it('stores a direct flight with prefixed flight number', function () {
    $payload = validDirectFlightPayload();

    $this->actingAs($this->user)
        ->post(route('admin.flights.store'), $payload)
        ->assertRedirect(route('admin.flights.index'));

    $flight = Flight::query()->first();

    expect($flight)->not->toBeNull()
        ->and($flight->flight_type)->toBe(FlightType::Direct)
        ->and($flight->direction)->toBe(FlightDirection::Outbound)
        ->and($flight->departure_flight_no)->toBe('PK740')
        ->and($flight->via_city_id)->toBeNull()
        ->and($flight->via_total_stay_minutes)->toBeNull();
});

it('stores an indirect flight and calculates total stay', function () {
    $payload = validIndirectFlightPayload();

    $this->actingAs($this->user)
        ->post(route('admin.flights.store'), $payload)
        ->assertRedirect(route('admin.flights.index'));

    $flight = Flight::query()->first();

    expect($flight->flight_type)->toBe(FlightType::Indirect)
        ->and($flight->via_departure_flight_no)->toBe('EK512')
        ->and($flight->via_total_stay_minutes)->toBe(330)
        ->and($flight->via_total_stay_label)->toBe('5h 30m');
});

it('rejects indirect flight when via departure is before arrival', function () {
    $payload = validIndirectFlightPayload([
        'via_departure_time' => '09:00',
    ]);

    $this->actingAs($this->user)
        ->from(route('admin.flights.create'))
        ->post(route('admin.flights.store'), $payload)
        ->assertRedirect(route('admin.flights.create'))
        ->assertSessionHasErrors('via_departure_time');
});

it('updates a flight', function () {
    $flight = Flight::factory()->create([
        'departure_city_id' => $this->departureCity->id,
        'departure_airport_id' => $this->departureAirport->id,
        'departure_airline_id' => $this->departureAirline->id,
        'arrival_city_id' => $this->arrivalCity->id,
        'arrival_airport_id' => $this->arrivalAirport->id,
    ]);

    $payload = validDirectFlightPayload([
        'departure_flight_number' => '999',
    ]);

    $this->actingAs($this->user)
        ->put(route('admin.flights.update', $flight), $payload)
        ->assertRedirect(route('admin.flights.index'));

    expect($flight->fresh()->departure_flight_no)->toBe('PK999');
});

it('deletes a flight', function () {
    $flight = Flight::factory()->create([
        'departure_city_id' => $this->departureCity->id,
        'departure_airport_id' => $this->departureAirport->id,
        'departure_airline_id' => $this->departureAirline->id,
        'arrival_city_id' => $this->arrivalCity->id,
        'arrival_airport_id' => $this->arrivalAirport->id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('admin.flights.destroy', $flight))
        ->assertRedirect(route('admin.flights.index'));

    expect(Flight::query()->count())->toBe(0);
});

it('builds flight numbers in the service', function () {
    $service = app(FlightService::class);

    expect($service->buildFlightNumber($this->departureAirline, '740'))->toBe('PK740')
        ->and($service->buildFlightNumber($this->departureAirline, 'PK740'))->toBe('PK740')
        ->and($service->calculateStayMinutes(
            now()->addWeek()->toDateString(),
            '11:00',
            now()->addWeek()->toDateString(),
            '16:30',
        ))->toBe(330);
});

it('requires flight direction on create', function () {
    $payload = validDirectFlightPayload();
    unset($payload['direction']);

    $this->actingAs($this->user)
        ->from(route('admin.flights.create'))
        ->post(route('admin.flights.store'), $payload)
        ->assertRedirect(route('admin.flights.create'))
        ->assertSessionHasErrors('direction');
});

it('stores a return from hajj flight', function () {
    $payload = validDirectFlightPayload([
        'direction' => FlightDirection::Return->value,
    ]);

    $this->actingAs($this->user)
        ->post(route('admin.flights.store'), $payload)
        ->assertRedirect(route('admin.flights.index'));

    expect(Flight::query()->first()?->direction)->toBe(FlightDirection::Return);
});

it('shows assigned hujaj count on flight index', function () {
    $flight = Flight::factory()->create([
        'departure_city_id' => $this->departureCity->id,
        'departure_airport_id' => $this->departureAirport->id,
        'departure_airline_id' => $this->departureAirline->id,
        'arrival_city_id' => $this->arrivalCity->id,
        'arrival_airport_id' => $this->arrivalAirport->id,
    ]);

    $pilgrim = Pilgrim::query()->create([
        'hajj_year' => now()->year,
    ]);

    $flight->pilgrims()->attach($pilgrim->id);

    expect($flight->fresh()->loadCount('pilgrims')->pilgrims_count)->toBe(1);

    $this->actingAs($this->user)
        ->get(route('admin.flights.index'))
        ->assertOk()
        ->assertSee('Departure to Hajj')
        ->assertSee($flight->departure_flight_no);
});

it('denies flight access without permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.flights.index'))
        ->assertForbidden();
});
