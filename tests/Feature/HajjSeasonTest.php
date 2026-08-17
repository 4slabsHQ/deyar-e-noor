<?php

use App\Enums\HajjSeasonStatus;
use App\Models\Company;
use App\Models\HajjSeason;
use App\Models\Pilgrim;
use App\Models\User;
use App\Services\HajjSeasonService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');
});

test('super admin can view hajj seasons page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.hajj-seasons.index'))
        ->assertOk()
        ->assertSee('Hajj Seasons')
        ->assertSee('Hajj '.now()->year);
});

test('super admin can add a hajj season', function () {
    $nextYear = now()->year + 1;

    $this->actingAs($this->admin)
        ->post(route('admin.hajj-seasons.store'), ['year' => $nextYear])
        ->assertRedirect(route('admin.hajj-seasons.index'));

    expect(HajjSeason::query()->where('year', $nextYear)->exists())->toBeTrue();
});

test('activating a season archives the previous active season', function () {
    $currentYear = now()->year;
    $nextYear = $currentYear + 1;

    $currentSeason = HajjSeason::query()->where('year', $currentYear)->firstOrFail();
    $nextSeason = HajjSeason::factory()->create(['year' => $nextYear]);

    $this->actingAs($this->admin)
        ->post(route('admin.hajj-seasons.activate', $nextSeason))
        ->assertRedirect(route('admin.hajj-seasons.index'));

    expect($currentSeason->fresh()->status)->toBe(HajjSeasonStatus::Archived)
        ->and($nextSeason->fresh()->status)->toBe(HajjSeasonStatus::Active)
        ->and(HajjSeason::query()->where('status', HajjSeasonStatus::Active)->count())->toBe(1);
});

test('dashboard uses the active hajj season year', function () {
    $activeYear = now()->year + 1;

    HajjSeason::factory()->create(['year' => $activeYear]);

    $this->actingAs($this->admin)
        ->post(route('admin.hajj-seasons.activate', HajjSeason::query()->where('year', $activeYear)->first()))
        ->assertRedirect(route('admin.hajj-seasons.index'));

    $company = Company::factory()->create([
        'quota' => 100,
        'is_active' => true,
    ]);

    Pilgrim::query()->create([
        'company_id' => $company->id,
        'hajj_year' => $activeYear,
        'booking_date' => now(),
    ]);

    Pilgrim::query()->create([
        'company_id' => $company->id,
        'hajj_year' => now()->year,
        'booking_date' => now()->subYear(),
    ]);

    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Hajj '.$activeYear)
        ->assertSee('1%');
});

test('hajj season service falls back to configured default year', function () {
    config(['hajj.default_active_year' => 2027]);

    HajjSeason::query()->delete();

    expect(app(HajjSeasonService::class)->activeYear())->toBe(2027);
});

test('registration staff cannot manage hajj seasons', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('hajj-seasons.view');

    $this->actingAs($user)
        ->get(route('admin.hajj-seasons.index'))
        ->assertOk();

    $this->actingAs($user)
        ->post(route('admin.hajj-seasons.store'), ['year' => now()->year + 2])
        ->assertForbidden();
});
