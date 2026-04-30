<x-guest-layout>
    <x-slot name="title">Sign Up</x-slot>
    <x-slot name="metaDescription">Create a new {{ $appSettings->get('app_name', config('app.name')) }} account.</x-slot>

    <div style="text-align: center; margin-bottom: 24px;">
        <a href="{{ route('home') }}" style="display: inline-block; font-size: 24px; font-weight: 700; color: var(--text-primary); text-decoration: none;">
            {{ $appSettings->get('app_name', config('app.name')) }}
        </a>
    </div>

    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 20px; font-weight: 600; margin: 0 0 4px 0; color: var(--text-primary);">Create your account</h1>
        <p style="font-size: 14px; color: var(--text-muted); margin: 0;">Sign up to get started</p>
    </div>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <x-input label="Name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" />

        <x-input label="Email address" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" />

        <div style="position: relative;">
            <x-input label="Password" name="password" type="password" required autocomplete="new-password" />
            <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';" style="position: absolute; right: 12px; top: 32px; width: 32px; height: 32px; background: transparent; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
            <div style="margin-top: 8px; display: flex; gap: 4px;">
                <div id="strength-1" style="height: 4px; flex-grow: 1; background: var(--border); border-radius: 2px;"></div>
                <div id="strength-2" style="height: 4px; flex-grow: 1; background: var(--border); border-radius: 2px;"></div>
                <div id="strength-3" style="height: 4px; flex-grow: 1; background: var(--border); border-radius: 2px;"></div>
            </div>
            <p id="password-strength-text" style="font-size: 12px; color: var(--text-muted); margin-top: 4px; margin-bottom: 0;">Password strength</p>
        </div>

        <x-input label="Confirm Password" name="password_confirmation" type="password" required autocomplete="new-password" />

        <x-button type="submit" variant="primary" style="width: 100%;">Create account</x-button>
    </form>

    @if($appSettings->get('enable_google_oauth', true))
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
        Already have an account? <a href="{{ route('login') }}" style="color: var(--accent); text-decoration: none;">Sign in</a>
    </div>

    <script>
        document.getElementById('password').addEventListener('input', function(e) {
            const val = e.target.value;
            let strength = 0;
            if (val.length > 5) strength++;
            if (val.length > 7 && val.match(/[0-9]/)) strength++;
            if (val.length > 8 && val.match(/[^a-zA-Z0-9]/)) strength++;
            
            const b1 = document.getElementById('strength-1');
            const b2 = document.getElementById('strength-2');
            const b3 = document.getElementById('strength-3');
            const text = document.getElementById('password-strength-text');
            
            b1.style.background = strength > 0 ? (strength === 1 ? 'var(--danger)' : (strength === 2 ? 'var(--warning)' : 'var(--success)')) : 'var(--border)';
            b2.style.background = strength > 1 ? (strength === 2 ? 'var(--warning)' : 'var(--success)') : 'var(--border)';
            b3.style.background = strength > 2 ? 'var(--success)' : 'var(--border)';
            
            if (val.length === 0) text.textContent = 'Password strength';
            else if (strength === 0 || strength === 1) text.textContent = 'Weak';
            else if (strength === 2) text.textContent = 'Good';
            else text.textContent = 'Strong';
        });
    </script>
</x-guest-layout>
