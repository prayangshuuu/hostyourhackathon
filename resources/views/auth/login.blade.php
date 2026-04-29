<x-guest-layout>
    <x-slot name="title">Sign In</x-slot>
    <x-slot name="metaDescription">Sign in to your HostYourHackathon account.</x-slot>

    <div style="text-align: center; margin-bottom: 24px;">
        <a href="{{ route('home') }}" style="display: inline-block; font-size: 24px; font-weight: 700; color: var(--text-primary); text-decoration: none;">
            HostYourHackathon
        </a>
    </div>

    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 20px; font-weight: 600; margin: 0 0 4px 0; color: var(--text-primary);">Welcome back</h1>
        <p style="font-size: 14px; color: var(--text-muted); margin: 0;">Sign in to your account</p>
    </div>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <x-input label="Email address" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" />

        <div style="position: relative;">
            <x-input label="Password" name="password" type="password" required autocomplete="current-password" />
            <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';" style="position: absolute; right: 12px; top: 32px; width: 32px; height: 32px; background: transparent; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between;">
            <label for="remember_me" style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-secondary); cursor: pointer;">
                <input id="remember_me" type="checkbox" name="remember" style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid var(--border); accent-color: var(--accent);">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size: 13px; color: var(--accent); text-decoration: none;">
                    Forgot password?
                </a>
            @endif
        </div>

        <x-button type="submit" variant="primary" style="width: 100%;">Sign in</x-button>
    </form>

    <div style="position: relative; margin: 24px 0; text-align: center;">
        <div style="position: absolute; top: 50%; left: 0; right: 0; border-top: 1px solid var(--border);"></div>
        <span style="position: relative; background: var(--surface); padding: 0 12px; font-size: 14px; color: var(--text-muted);">or</span>
    </div>

    <x-button href="{{ route('auth.google') }}" variant="secondary" style="width: 100%; display: flex; justify-content: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25C22.56 11.47 22.49 10.72 22.36 10H12V14.26H17.92C17.66 15.63 16.88 16.79 15.71 17.57V20.34H19.28C21.36 18.42 22.56 15.6 22.56 12.25Z" fill="#4285F4"/>
            <path d="M12 23C14.97 23 17.46 22.02 19.28 20.34L15.71 17.57C14.73 18.23 13.48 18.64 12 18.64C9.14 18.64 6.71 16.71 5.84 14.1H2.18V16.94C3.99 20.53 7.7 23 12 23Z" fill="#34A853"/>
            <path d="M5.84 14.1C5.62 13.44 5.49 12.74 5.49 12C5.49 11.26 5.62 10.56 5.84 9.9V7.06H2.18C1.43 8.55 1 10.22 1 12C1 13.78 1.43 15.45 2.18 16.94L5.84 14.1Z" fill="#FBBC05"/>
            <path d="M12 5.36C13.62 5.36 15.07 5.92 16.22 7.02L19.36 3.88C17.46 2.1 14.97 1 12 1C7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.29 9.14 5.36 12 5.36Z" fill="#EA4335"/>
        </svg>
        Continue with Google
    </x-button>

    <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-secondary);">
        Don't have an account? <a href="{{ route('register') }}" style="color: var(--accent); text-decoration: none;">Sign up</a>
    </div>
</x-guest-layout>
