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

    @if($settings->get('enable_google_oauth', true))
    <div style="display: flex; align-items: center; margin: 24px 0;">
        <div style="flex: 1; height: 1px; background: var(--border);"></div>
        <div style="padding: 0 16px; color: var(--text-muted); font-size: 14px; font-weight: 500;">OR</div>
        <div style="flex: 1; height: 1px; background: var(--border);"></div>
    </div>

    <x-button href="{{ route('auth.google') }}" variant="secondary" style="width: 100%; display: flex; justify-content: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Continue with Google
    </x-button>
    @endif

    <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-secondary);">
        Don't have an account? <a href="{{ route('register') }}" style="color: var(--accent); text-decoration: none;">Sign up</a>
    </div>
</x-guest-layout>
