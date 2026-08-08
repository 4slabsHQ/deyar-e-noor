<?php

use App\Models\Airline;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Admin');
});

test('admin can remove airline logo on update', function () {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('logo.jpg')->store('airlines', 'public');

    $airline = Airline::query()->create([
        'name' => 'Test Air',
        'code' => 'TA',
        'logo' => $path,
        'is_active' => true,
        'created_by' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)->put(route('admin.airlines.update', $airline), [
        'name' => 'Test Air',
        'code' => 'TA',
        'is_active' => '1',
        'remove_logo' => '1',
    ]);

    $response->assertRedirect(route('admin.airlines.index'));

    expect($airline->fresh()->logo)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
