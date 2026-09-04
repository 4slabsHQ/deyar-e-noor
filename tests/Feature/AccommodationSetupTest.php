<?php

use App\Enums\AccommodationPlanSlot;
use App\Enums\AccommodationPlanType;
use App\Enums\PackageDuration;
use App\Enums\PropertyCity;
use App\Enums\PropertyType;
use App\Enums\RoutePointType;
use App\Models\AccommodationPlan;
use App\Models\Airport;
use App\Models\City;
use App\Models\Package;
use App\Models\Property;
use App\Models\Route;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

test('admin can create property with akads', function () {
    $this->actingAs($this->user)->post(route('admin.properties.store'), [
        'name' => 'Swissotel Makkah',
        'city' => PropertyCity::Makkah->value,
        'type' => PropertyType::Hotel->value,
        'is_active' => '1',
        'akads' => [
            ['akad_number' => 'MK-2027-001', 'label' => 'Block A'],
            ['akad_number' => 'MK-2027-002', 'label' => 'Block B'],
        ],
    ])->assertRedirect(route('admin.properties.index'));

    $property = Property::query()->where('name', 'Swissotel Makkah')->first();

    expect($property)->not->toBeNull()
        ->and($property->akads)->toHaveCount(2);
});

test('property form supports makkah shifting city and building type', function () {
    $this->actingAs($this->user)->post(route('admin.properties.store'), [
        'name' => 'Makkah Shifting Block A',
        'city' => PropertyCity::MakkahShifting->value,
        'type' => PropertyType::Building->value,
        'is_active' => '1',
        'akads' => [
            ['akad_number' => 'MS-001', 'label' => 'Tower 1'],
        ],
    ])->assertRedirect(route('admin.properties.index'));

    $property = Property::query()->where('name', 'Makkah Shifting Block A')->first();

    expect($property)->not->toBeNull()
        ->and($property->city)->toBe(PropertyCity::MakkahShifting)
        ->and($property->type)->toBe(PropertyType::Building);
});

test('admin can create route with variable steps', function () {
    $airport = Airport::factory()->create(['name' => 'King Abdulaziz International', 'code' => 'JED']);
    $makkah = City::factory()->create(['name' => 'Makkah']);
    $madinah = City::factory()->create(['name' => 'Madinah']);

    $this->actingAs($this->user)->post(route('admin.routes.store'), [
        'name' => 'Route 1',
        'is_active' => '1',
        'steps' => [
            ['point_type' => RoutePointType::Airport->value, 'airport_id' => $airport->id],
            ['point_type' => RoutePointType::City->value, 'city_id' => $makkah->id],
            ['point_type' => RoutePointType::City->value, 'city_id' => $madinah->id],
            ['point_type' => RoutePointType::Hajj->value],
            ['point_type' => RoutePointType::Airport->value, 'airport_id' => $airport->id],
        ],
    ])->assertRedirect(route('admin.routes.index'));

    $route = Route::query()->where('name', 'Route 1')->with(['steps.airport', 'steps.city'])->first();

    expect($route)->not->toBeNull()
        ->and($route->steps)->toHaveCount(5)
        ->and($route->summary())->toBe('King Abdulaziz International (JED) → Makkah → Madinah → Hajj → King Abdulaziz International (JED)');
});

test('admin can create still accommodation plan with building property', function () {
    $makkahHotel = Property::factory()->create([
        'name' => 'Makkah Hotel',
        'city' => PropertyCity::Makkah,
        'type' => PropertyType::Hotel,
    ]);
    $madinahBuilding = Property::factory()->create([
        'name' => 'Madinah Still Building',
        'city' => PropertyCity::Madinah,
        'type' => PropertyType::Building,
    ]);

    $this->actingAs($this->user)->post(route('admin.accommodation-plans.store'), [
        'name' => 'Still With Building',
        'type' => AccommodationPlanType::Still->value,
        'is_active' => '1',
        'slots' => [
            'makkah_hotel' => ['property_id' => $makkahHotel->id],
            'madinah_hotel' => ['property_id' => $madinahBuilding->id],
        ],
    ])->assertRedirect(route('admin.accommodation-plans.index'));

    $plan = AccommodationPlan::query()->where('name', 'Still With Building')->with('slots.property')->first();

    expect($plan)->not->toBeNull()
        ->and($plan->slots)->toHaveCount(2)
        ->and($plan->slots->firstWhere('slot', AccommodationPlanSlot::MadinahHotel)->property->type)
        ->toBe(PropertyType::Building);
});

test('admin can create shifting accommodation plan with building and sheesha properties', function () {
    $makkahHotel = Property::factory()->create([
        'city' => PropertyCity::Makkah,
        'type' => PropertyType::Hotel,
    ]);
    $shiftingSheesha = Property::factory()->create([
        'name' => 'Sheesha Hotel',
        'city' => PropertyCity::MakkahShifting,
        'type' => PropertyType::ShiftingBuilding,
    ]);
    $madinahHotel = Property::factory()->create([
        'city' => PropertyCity::Madinah,
        'type' => PropertyType::Hotel,
    ]);

    $this->actingAs($this->user)->post(route('admin.accommodation-plans.store'), [
        'name' => 'Shifting Plan',
        'type' => AccommodationPlanType::Shifting->value,
        'is_active' => '1',
        'slots' => [
            'makkah_hotel' => ['property_id' => $makkahHotel->id],
            'shifting_building' => ['property_id' => $shiftingSheesha->id],
            'madinah_hotel' => ['property_id' => $madinahHotel->id],
        ],
    ])->assertRedirect(route('admin.accommodation-plans.index'));

    expect(AccommodationPlan::query()->where('name', 'Shifting Plan')->exists())->toBeTrue();
});

test('accommodation plan create form lists building properties in city slots', function () {
    Property::factory()->create([
        'name' => 'Visible Madinah Building',
        'city' => PropertyCity::Madinah,
        'type' => PropertyType::Building,
    ]);

    $response = $this->actingAs($this->user)->get(route('admin.accommodation-plans.create'));

    $response->assertOk()
        ->assertSee('Visible Madinah Building');
});

test('admin can create still accommodation plan', function () {
    $makkah = Property::factory()->create([
        'name' => 'Makkah Hotel',
        'city' => PropertyCity::Makkah,
        'type' => PropertyType::Hotel,
    ]);
    $madinah = Property::factory()->create([
        'name' => 'Madinah Hotel',
        'city' => PropertyCity::Madinah,
        'type' => PropertyType::Hotel,
    ]);

    $this->actingAs($this->user)->post(route('admin.accommodation-plans.store'), [
        'name' => 'Standard Still',
        'type' => AccommodationPlanType::Still->value,
        'is_active' => '1',
        'slots' => [
            'makkah_hotel' => ['property_id' => $makkah->id],
            'madinah_hotel' => ['property_id' => $madinah->id],
        ],
    ])->assertRedirect(route('admin.accommodation-plans.index'));

    $plan = AccommodationPlan::query()->where('name', 'Standard Still')->with('slots')->first();

    expect($plan)->not->toBeNull()
        ->and($plan->slots)->toHaveCount(2);
});

test('admin can attach accommodation plan and route to package', function () {
    $makkah = Property::factory()->create(['city' => PropertyCity::Makkah, 'type' => PropertyType::Hotel]);
    $madinah = Property::factory()->create(['city' => PropertyCity::Madinah, 'type' => PropertyType::Hotel]);

    $plan = AccommodationPlan::factory()->create(['type' => AccommodationPlanType::Still]);
    $plan->slots()->createMany([
        ['slot' => 'makkah_hotel', 'property_id' => $makkah->id, 'sequence' => 1],
        ['slot' => 'madinah_hotel', 'property_id' => $madinah->id, 'sequence' => 2],
    ]);

    $route = Route::factory()->create();
    $airport = Airport::factory()->create(['name' => 'Jeddah Airport', 'code' => 'JED']);
    $makkah = City::factory()->create(['name' => 'Makkah']);
    $route->steps()->createMany([
        ['sequence' => 1, 'point_type' => RoutePointType::Airport, 'airport_id' => $airport->id],
        ['sequence' => 2, 'point_type' => RoutePointType::City, 'city_id' => $makkah->id],
    ]);

    $this->actingAs($this->user)->post(route('admin.packages.store'), [
        'number' => 'PKG-ACC-01',
        'name' => 'Gold Package',
        'price' => '850000',
        'days' => '21',
        'qurbani_included' => '1',
        'duration' => PackageDuration::Long->value,
        'accommodation_plan_id' => $plan->id,
        'route_id' => $route->id,
        'is_active' => '1',
    ])->assertRedirect(route('admin.packages.index'));

    $package = Package::query()->where('number', 'PKG-ACC-01')->first();

    expect($package)->not->toBeNull()
        ->and($package->accommodation_plan_id)->toBe($plan->id)
        ->and($package->route_id)->toBe($route->id);
});

test('accommodation setup index pages load', function () {
    foreach ([
        'admin.properties.index',
        'admin.routes.index',
        'admin.accommodation-plans.index',
    ] as $route) {
        $this->actingAs($this->user)->get(route($route))->assertOk();
    }
});
