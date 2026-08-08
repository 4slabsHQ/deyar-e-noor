<?php

namespace Database\Factories;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use App\Models\CareOff;
use App\Models\City;
use App\Models\Company;
use App\Models\FormOwner;
use App\Models\MaktabCategory;
use App\Models\MehramRelation;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\RoomType;
use App\Models\WarisRelation;
use App\Services\PilgrimService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pilgrim>
 */
class PilgrimFactory extends Factory
{
    protected $model = Pilgrim::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $hajjYear = (int) now()->year;
        $dob = Carbon::parse(fake()->dateTimeBetween('-70 years', '-25 years'));
        $service = app(PilgrimService::class);
        $surname = fake()->lastName();
        $givenName = fake()->firstName();

        return [
            'hajj_year' => $hajjYear,
            'booking_date' => now()->toDateString(),
            'form_owner_id' => FormOwner::factory(),
            'company_id' => Company::factory(),
            'maktab_category_id' => MaktabCategory::factory(),
            'package_id' => Package::factory(),
            'care_off_id' => CareOff::factory(),
            'pod_city_id' => City::factory(),
            'room_type_id' => RoomType::factory(),
            'gender' => fake()->randomElement(Gender::cases()),
            'surname' => $surname,
            'given_name' => $givenName,
            'father_husband_name' => fake()->name('male'),
            'full_name' => $service->buildFullName($surname, $givenName),
            'passport_no' => strtoupper(fake()->lexify('??')).fake()->numerify('#######'),
            'date_of_birth' => $dob->toDateString(),
            'birth_place' => fake()->city(),
            'passport_expiry' => now()->addYears(2)->toDateString(),
            'address' => fake()->address(),
            'mobile' => fake()->numerify('03##-#######'),
            'cnic' => fake()->numerify('#####-#######-#'),
            'blood_group' => fake()->randomElement(BloodGroup::cases()),
            'mehram_name' => fake()->name(),
            'mehram_relation_id' => MehramRelation::factory(),
            'waris_name' => fake()->name(),
            'waris_cnic' => fake()->numerify('#####-#######-#'),
            'waris_relation_id' => WarisRelation::factory(),
            'waris_mobile' => fake()->numerify('03##-#######'),
            'family_code' => 'TST-01-S',
            'family_number' => 1,
            'family_member_suffix' => 'S',
            'age' => $service->calculateAge($dob, $hajjYear),
        ];
    }
}
