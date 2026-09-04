<?php

use App\Enums\PackageDuration;
use App\Models\CareOff;
use App\Models\FormOwner;
use App\Models\MaktabCategory;
use App\Models\Package;
use App\Models\RoomType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

test('admin can create form owner', function () {
    $this->actingAs($this->user)->post(route('admin.form-owners.store'), [
        'name' => 'Agent One',
        'is_active' => '1',
    ])->assertRedirect(route('admin.form-owners.index'));

    expect(FormOwner::query()->where('name', 'Agent One')->exists())->toBeTrue();
});

test('admin can create maktab category', function () {
    $this->actingAs($this->user)->post(route('admin.maktab-categories.store'), [
        'name' => 'Category A',
        'zone' => 'Zone 1',
        'is_active' => '1',
    ])->assertRedirect(route('admin.maktab-categories.index'));

    expect(MaktabCategory::query()->where('name', 'Category A')->where('zone', 'Zone 1')->exists())->toBeTrue();
});

test('admin can create care off', function () {
    $this->actingAs($this->user)->post(route('admin.care-offs.store'), [
        'name' => 'Main Agent',
        'is_active' => '1',
    ])->assertRedirect(route('admin.care-offs.index'));

    expect(CareOff::query()->where('name', 'Main Agent')->exists())->toBeTrue();
});

test('admin can create package', function () {
    $this->actingAs($this->user)->post(route('admin.packages.store'), [
        'number' => 'PKG-001',
        'name' => 'Economy Package',
        'price' => '250000',
        'days' => '21',
        'qurbani_included' => '1',
        'duration' => PackageDuration::Long->value,
        'is_active' => '1',
    ])->assertRedirect(route('admin.packages.index'));

    $package = Package::query()->where('number', 'PKG-001')->first();

    expect($package)->not->toBeNull()
        ->and($package->duration)->toBe(PackageDuration::Long)
        ->and($package->qurbani_included)->toBeTrue();
});

test('admin can create room type', function () {
    $this->actingAs($this->user)->post(route('admin.room-types.store'), [
        'name' => 'Sharing',
        'is_active' => '1',
    ])->assertRedirect(route('admin.room-types.index'));

    expect(RoomType::query()->where('name', 'Sharing')->exists())->toBeTrue();
});

test('hajj master index pages load', function () {
    $routes = [
        'admin.form-owners.index',
        'admin.maktab-categories.index',
        'admin.care-offs.index',
        'admin.packages.index',
        'admin.properties.index',
        'admin.routes.index',
        'admin.accommodation-plans.index',
        'admin.room-types.index',
        'admin.mehram-relations.index',
        'admin.waris-relations.index',
    ];

    foreach ($routes as $route) {
        $this->actingAs($this->user)->get(route($route))->assertOk();
    }
});
