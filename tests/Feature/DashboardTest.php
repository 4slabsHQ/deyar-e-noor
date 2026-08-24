<?php

use App\Enums\PackageDuration;
use App\Models\Company;
use App\Models\FormOwner;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('dashboard shows quota metrics for users with company access', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $company = Company::factory()->create([
        'name' => 'Deyar-e-Noor',
        'code' => 'DYN',
        'quota' => 100,
        'is_active' => true,
    ]);

    Pilgrim::query()->create([
        'company_id' => $company->id,
        'hajj_year' => now()->year,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Quota Overview')
        ->assertSee('Total Quota')
        ->assertSee('Entered')
        ->assertSee('Remaining')
        ->assertSee('Refund')
        ->assertSee('Overall utilisation')
        ->assertSee('1%')
        ->assertSee('Company Quota Utilisation')
        ->assertSee('Package Limit Utilisation')
        ->assertSee('Form Owner Limit Utilisation')
        ->assertSee('deyar-quota-progress__fill')
        ->assertSee('Recent Registrations')
        ->assertSee('View all')
        ->assertSee('Deyar-e-Noor')
        ->assertDontSee('Signed in as')
        ->assertDontSee('Registrations (Last 6 Months)');
});

test('dashboard lists package utilisation in package number order', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $company = Company::factory()->create();

    $packageThree = Package::create([
        'number' => 'PKG-003',
        'name' => 'Package Three',
        'price' => 850000,
        'days' => 21,
        'qurbani_included' => true,
        'duration' => PackageDuration::Long,
        'limit' => 10,
        'is_active' => true,
    ]);

    $packageOne = Package::create([
        'number' => 'PKG-001',
        'name' => 'Package One',
        'price' => 850000,
        'days' => 21,
        'qurbani_included' => true,
        'duration' => PackageDuration::Long,
        'limit' => 10,
        'is_active' => true,
    ]);

    $packageTwo = Package::create([
        'number' => 'PKG-002',
        'name' => 'Package Two',
        'price' => 850000,
        'days' => 21,
        'qurbani_included' => true,
        'duration' => PackageDuration::Long,
        'limit' => 10,
        'is_active' => true,
    ]);

    foreach ([$packageThree, $packageOne, $packageTwo] as $package) {
        Pilgrim::query()->create([
            'company_id' => $company->id,
            'package_id' => $package->id,
            'hajj_year' => now()->year,
        ]);
    }

    Pilgrim::query()->create([
        'company_id' => $company->id,
        'package_id' => $packageThree->id,
        'hajj_year' => now()->year,
    ]);

    $content = $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->getContent();

    expect($content)->toContain('Package Limit Utilisation')
        ->and(strpos($content, $packageOne->registrationOptionLabel()))->toBeLessThan(strpos($content, $packageTwo->registrationOptionLabel()))
        ->and(strpos($content, $packageTwo->registrationOptionLabel()))->toBeLessThan(strpos($content, $packageThree->registrationOptionLabel()));
});

test('dashboard shows package and form owner utilisation when limits are configured', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $package = Package::create([
        'number' => 'PKG-100',
        'name' => 'Economy Package',
        'price' => 850000,
        'days' => 21,
        'qurbani_included' => true,
        'duration' => PackageDuration::Long,
        'limit' => 50,
        'is_active' => true,
    ]);

    $formOwner = FormOwner::create([
        'name' => 'Self',
        'limit' => 25,
        'is_active' => true,
    ]);

    Pilgrim::query()->create([
        'company_id' => Company::factory()->create()->id,
        'package_id' => $package->id,
        'form_owner_id' => $formOwner->id,
        'hajj_year' => now()->year,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee($package->registrationOptionLabel(), false)
        ->assertSee('Self')
        ->assertSee('1/50')
        ->assertSee('1/25');
});

test('dashboard hides pilgrim widgets without pilgrim permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('companies.view');

    Company::factory()->create(['quota' => 50, 'is_active' => true]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Quota Overview')
        ->assertDontSee('Recent Registrations')
        ->assertDontSee('Registrations (Last 6 Months)');
});

test('admin can save package and form owner limits', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)->post(route('admin.packages.store'), [
        'number' => 'PKG-200',
        'name' => 'Premium Package',
        'price' => '950000',
        'days' => '18',
        'qurbani_included' => '1',
        'duration' => PackageDuration::Short->value,
        'limit' => '40',
        'is_active' => '1',
    ])->assertRedirect(route('admin.packages.index'));

    $this->actingAs($user)->post(route('admin.form-owners.store'), [
        'name' => 'Agent One',
        'limit' => '15',
        'is_active' => '1',
    ])->assertRedirect(route('admin.form-owners.index'));

    expect(Package::query()->where('number', 'PKG-200')->value('limit'))->toBe(40)
        ->and(FormOwner::query()->where('name', 'Agent One')->value('limit'))->toBe(15);
});
