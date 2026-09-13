<?php

namespace Tests\Support;

use App\Enums\AccommodationPlanType;
use App\Enums\PropertyCity;
use App\Enums\PropertyType;
use App\Enums\RoutePointType;
use App\Models\AccommodationPlan;
use App\Models\Airport;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyAkad;
use App\Models\Route;

class PackageRegistrationSetup
{
    /** @return array{accommodation_plan_id: int, route_id: int, plan: AccommodationPlan, route: Route, makkah_akad: PropertyAkad} */
    public static function still(int $hajjYear): array
    {
        $makkahProperty = Property::factory()->create([
            'name' => 'Default Makkah Hotel',
            'city' => PropertyCity::Makkah,
            'type' => PropertyType::Hotel,
            'hajj_year' => $hajjYear,
        ]);
        $makkahAkad = $makkahProperty->akads()->create([
            'akad_number' => 'MK-DEFAULT-001',
            'label' => 'Tower A',
        ]);
        $madinahProperty = Property::factory()->create([
            'name' => 'Default Madinah Hotel',
            'city' => PropertyCity::Madinah,
            'type' => PropertyType::Hotel,
            'hajj_year' => $hajjYear,
        ]);

        $plan = AccommodationPlan::factory()->create([
            'name' => 'Default Still Plan',
            'type' => AccommodationPlanType::Still,
            'hajj_year' => $hajjYear,
        ]);
        $plan->slots()->createMany([
            ['slot' => 'makkah_hotel', 'property_id' => $makkahProperty->id, 'property_akad_id' => $makkahAkad->id, 'sequence' => 1],
            ['slot' => 'madinah_hotel', 'property_id' => $madinahProperty->id, 'sequence' => 2],
        ]);

        $route = Route::factory()->create([
            'name' => 'Default Route',
            'hajj_year' => $hajjYear,
        ]);
        $airport = Airport::factory()->create(['name' => 'Default Airport', 'code' => 'JED']);
        $makkahCity = City::factory()->create(['name' => 'Makkah City']);
        $route->steps()->createMany([
            ['sequence' => 1, 'point_type' => RoutePointType::Airport, 'airport_id' => $airport->id],
            ['sequence' => 2, 'point_type' => RoutePointType::City, 'city_id' => $makkahCity->id],
        ]);

        return [
            'accommodation_plan_id' => $plan->id,
            'route_id' => $route->id,
            'plan' => $plan,
            'route' => $route,
            'makkah_akad' => $makkahAkad,
        ];
    }
}
