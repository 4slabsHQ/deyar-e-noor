<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('admin.profile.edit'))
        ->assertOk()
        ->assertSee('Account', false)
        ->assertSee('Password', false);
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('admin.profile.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('user can upload a profile photo', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => UploadedFile::fake()->image('profile.jpg'),
        ])
        ->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->photo_path)->not->toBeNull()
        ->and($user->photo_url)->not->toBeNull();

    Storage::disk('public')->assertExists($user->photo_path);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee($user->photo_url, false);
});

test('user can remove their profile photo', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $path = 'users/photos/test-profile.jpg';
    Storage::disk('public')->put($path, 'fake-image');
    $user->update(['photo_path' => $path]);

    $this->actingAs($user)
        ->patch(route('admin.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'remove_photo' => '1',
        ])
        ->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHasNoErrors();

    expect($user->fresh()->photo_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('user can change their password from profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHas('status', 'password-updated');

    expect(Hash::check('new-secure-password', $user->fresh()->password))->toBeTrue();
});

test('user must provide current password to change password', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHasErrors('current_password', null, 'updatePassword');
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('admin.profile.update'), [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.profile.edit'));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $path = 'users/photos/test-profile.jpg';
    Storage::disk('public')->put($path, 'fake-image');
    $user->update(['photo_path' => $path]);

    $response = $this
        ->actingAs($user)
        ->delete(route('admin.profile.destroy'), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    expect(auth()->guest())->toBeTrue()
        ->and(User::query()->find($user->id))->toBeNull();

    Storage::disk('public')->assertMissing($path);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->delete(route('admin.profile.destroy'), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('admin.profile.edit'));

    expect($user->fresh())->not->toBeNull();
});
