<?php

use App\Models\FormOwner;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

test('form owners index lists all records for datatables', function () {
    $formOwners = collect(range(1, 16))->map(function (int $number) {
        return FormOwner::query()->create([
            'name' => "Form Owner {$number}",
            'is_active' => true,
        ]);
    });

    $response = $this->actingAs($this->user)->get(route('admin.form-owners.index'))
        ->assertOk();

    foreach ($formOwners as $formOwner) {
        $response->assertSee($formOwner->name, false);
    }
});

test('users index lists all records for datatables', function () {
    $users = User::factory()->count(16)->create();

    $response = $this->actingAs($this->user)->get(route('admin.users.index'))
        ->assertOk();

    foreach ($users as $user) {
        $response->assertSee($user->email, false);
    }
});
