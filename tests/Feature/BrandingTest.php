<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

test('admin table actions use semantic hci button classes', function () {
    $html = view('components.admin.table-actions', [
        'viewRoute' => '/view',
        'editRoute' => '/edit',
        'deleteRoute' => '/delete',
    ])->render();

    expect($html)
        ->toContain('btn-info')
        ->toContain('btn-warning')
        ->toContain('btn-danger')
        ->not->toContain('btn-primary shadow btn-xs');
});

test('login page shows deyar-e-noor branding', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('images/logo.png', false);
    $response->assertSee('Welcome back', false);
    $response->assertSee('DEYAR-E-NOOR', false);
    $response->assertSee('Hajj Umrah &amp; Services (Pvt) Ltd.', false);
    $response->assertSee('login-brand-panel', false);
    $response->assertSee('deyar-brand.css', false);
    $response->assertSee('login.css', false);
    $response->assertDontSee('login-logo-wrap', false);
});

test('authenticated dashboard shows deyar-e-noor branding', function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Deyar-e-Noor', false);
    $response->assertSee('deyar-brand.css', false);
    $response->assertSee('deyar-metric__label', false);
    $response->assertSee('deyar-panel-card', false);
});
