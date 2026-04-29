<x-guest-layout>
    <x-slot name="title">Sign Up</x-slot>
    <x-slot name="metaDescription">Create a new HostYourHackathon account.</x-slot>

    <div style="text-align: center; margin-bottom: 24px;">
        <a href="{{ route('home') }}" style="display: inline-block; font-size: 24px; font-weight: 700; color: var(--text-primary); text-decoration: none;">
            HostYourHackathon
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
