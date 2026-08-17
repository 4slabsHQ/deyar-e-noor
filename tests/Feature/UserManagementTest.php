<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->assignRole('Super Admin');
});

test('super admin can delete another user', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($this->superAdmin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    expect(User::query()->whereKey($user->id)->exists())->toBeFalse();
});

test('super admin cannot delete their own account', function () {
    $this->actingAs($this->superAdmin)
        ->from(route('admin.users.index'))
        ->delete(route('admin.users.destroy', $this->superAdmin))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('error');

    expect(User::query()->whereKey($this->superAdmin->id)->exists())->toBeTrue();
});

test('super admin cannot delete the last super admin account', function () {
    $this->actingAs($this->superAdmin)
        ->from(route('admin.users.index'))
        ->delete(route('admin.users.destroy', $this->superAdmin))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('error');
});

test('users index shows delete action for other users', function () {
    $otherUser = User::factory()->create(['name' => 'Other User']);
    $otherUser->assignRole('Super Admin');

    $this->actingAs($this->superAdmin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('admin/users/'.$otherUser->id, false)
        ->assertSee('method="POST"', false);
});

test('user without delete permission cannot delete users', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo('users.view');

    $target = User::factory()->create();
    $target->assignRole('Super Admin');

    $this->actingAs($actor)
        ->delete(route('admin.users.destroy', $target))
        ->assertForbidden();
});
