<x-guest-layout>
    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="login-mobile-logo">

    <div class="login-card">
        <h1 class="login-form-title">Welcome back</h1>
        <p class="login-form-subtitle">Sign in to your account</p>

        @if (session('status'))
            <div class="login-status">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="login-error mb-3">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="login-field">
                <label for="email" class="login-field-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       autocomplete="username" class="login-input @error('email') login-input-error @enderror">
                @error('email')
                    <p class="login-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="login-field">
                <label for="password" class="login-field-label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="login-input @error('password') login-input-error @enderror">
                @error('password')
                    <p class="login-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="login-options">
                <label for="remember_me" class="login-remember">
                    <input id="remember_me" type="checkbox" name="remember">
                    Remember me
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="login-forgot">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="login-btn">Sign in</button>
        </form>
    </div>
</x-guest-layout>
