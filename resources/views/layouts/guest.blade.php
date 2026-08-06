<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }} — Login</title>

        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="{{ asset('css/login.css') }}" rel="stylesheet">
    </head>
    <body style="font-family: Figtree, ui-sans-serif, system-ui, sans-serif;">
        <div class="login-page">
            <aside class="login-brand-panel">
                <div class="login-brand-shape"></div>

                <div class="login-brand-inner">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('branding.title') }}" class="login-brand-logo">
                    <h1 class="login-brand-title">{{ config('branding.title') }}</h1>
                    <p class="login-brand-subtitle">{{ config('branding.subtitle') }}</p>
                </div>

                <p class="login-brand-copy">&copy; {{ now()->year }} {{ config('app.name') }}</p>
            </aside>

            <main class="login-form-panel">
                <div class="login-form-inner">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
