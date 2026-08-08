<?php

use App\Models\User;

test('login page shows deyar-e-noor branding', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('images/logo.png', false);
    $response->assertSee('Welcome back', false);
    $response->assertSee('DEYAR-E-NOOR', false);
    $response->assertSee('Hajj Umrah &amp; Services (Pvt) Ltd.', false);
    $response->assertSee('login-brand-panel', false);
    $response->assertDontSee('login-logo-wrap', false);
});

test('authenticated dashboard shows deyar-e-noor branding', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Deyar-e-Noor', false);
    $response->assertSee('deyar-brand.css', false);
    $response->assertSee('deyar-metric__label', false);
});
