<?php

use App\Models\Company;
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

test('admin can create a company with hajj fields', function () {
    $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
        'name' => 'Deyar-e-Noor',
        'code' => 'dyn',
        'enr_number' => 'ENR-001',
        'munazzam_code' => 'MZ-100',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.companies.index'));

    $company = Company::query()->first();

    expect($company)->not->toBeNull()
        ->and($company->code)->toBe('DYN')
        ->and($company->enr_number)->toBe('ENR-001')
        ->and($company->munazzam_code)->toBe('MZ-100');
});

test('admin can update company hajj fields', function () {
    $company = Company::query()->create([
        'name' => 'Test Co',
        'code' => 'TST',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->put(route('admin.companies.update', $company), [
        'name' => 'Test Co',
        'code' => 'TST',
        'enr_number' => 'ENR-999',
        'munazzam_code' => 'MZ-999',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.companies.index'));

    expect($company->fresh()->enr_number)->toBe('ENR-999');
});

test('company code must be unique', function () {
    Company::query()->create([
        'name' => 'Existing',
        'code' => 'DYN',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->from(route('admin.companies.create'))
        ->post(route('admin.companies.store'), [
            'name' => 'Another',
            'code' => 'DYN',
            'is_active' => '1',
        ]);

    $response->assertRedirect(route('admin.companies.create'));
    $response->assertSessionHasErrors('code');
});

test('admin can remove company logo on update', function () {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('logo.jpg')->store('companies/logos', 'public');

    $company = Company::query()->create([
        'name' => 'Test Co',
        'code' => 'TST',
        'logo' => $path,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->put(route('admin.companies.update', $company), [
        'name' => 'Test Co',
        'code' => 'TST',
        'is_active' => '1',
        'remove_logo' => '1',
    ]);

    $response->assertRedirect(route('admin.companies.index'));

    expect($company->fresh()->logo)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
