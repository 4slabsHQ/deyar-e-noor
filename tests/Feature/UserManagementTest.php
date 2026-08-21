<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('super admin can update user details and role', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
    $user->assignRole('Super Admin');

    $this->actingAs($this->superAdmin)
        ->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'Super Admin',
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $user->refresh();

    expect($user->name)->toBe('Updated Name')
        ->and($user->email)->toBe('updated@example.com')
        ->and($user->hasRole('Super Admin'))->toBeTrue();
});

test('super admin can reset another users password', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $originalHash = $user->password;

    $this->actingAs($this->superAdmin)
        ->put(route('admin.users.password.update', $user), [
            'password' => 'new-password-1',
            'password_confirmation' => 'new-password-1',
        ])
        ->assertRedirect(route('admin.users.edit', $user))
        ->assertSessionHas('status', 'password-updated');

    expect($user->fresh()->password)->not->toBe($originalHash);
});

test('edit user page shows separate password section', function () {
    $user = User::factory()->create(['name' => 'Password Section User']);
    $user->assignRole('Super Admin');

    $this->actingAs($this->superAdmin)
        ->get(route('admin.users.edit', $user))
        ->assertOk()
        ->assertSee('Password Section User', false)
        ->assertSee('Password', false)
        ->assertSee('admin/users/'.$user->id.'/password', false);
});

test('super admin can upload a user photo on create and edit', function () {
    Storage::fake('public');

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.store'), [
            'name' => 'Photo User',
            'email' => 'photo@example.com',
            'password' => 'password-1',
            'password_confirmation' => 'password-1',
            'role' => 'Super Admin',
            'photo' => UploadedFile::fake()->image('user.jpg'),
        ])
        ->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'photo@example.com')->firstOrFail();

    expect($user->photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($user->photo_path);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee($user->photo_url, false);

    $this->actingAs($this->superAdmin)
        ->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'Super Admin',
            'remove_photo' => '1',
        ])
        ->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->photo_path)->toBeNull();
});

test('inactive users cannot log in', function () {
    User::factory()->inactive()->create([
        'email' => 'inactive@example.com',
        'password' => bcrypt('Password@123'),
    ]);

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'inactive@example.com',
            'password' => 'Password@123',
        ])
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('super admin can deactivate a user', function () {
    $user = User::factory()->create(['name' => 'Inactive Target']);
    $user->assignRole('Super Admin');

    $this->actingAs($this->superAdmin)
        ->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'Super Admin',
            'is_active' => '0',
        ])
        ->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->is_active)->toBeFalse();
});

test('deactivated user is logged out when accessing admin', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertOk();

    $user->update(['is_active' => false]);

    $this->actingAs($user->fresh())
        ->get(route('admin.users.index'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error');
});
