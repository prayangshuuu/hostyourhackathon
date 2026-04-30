<x-guest-layout>
    <x-slot name="title">Set New Password</x-slot>
    <x-slot name="metaDescription">Set a new password for your {{ $appSettings->get('app_name', config('app.name')) }} account.</x-slot>

    <div style="text-align: center; margin-bottom: 24px;">
        <a href="{{ route('home') }}" style="display: inline-block; font-size: 24px; font-weight: 700; color: var(--text-primary); text-decoration: none;">
            {{ $appSettings->get('app_name', config('app.name')) }}
        </a>
    </div>

    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 20px; font-weight: 600; margin: 0 0 4px 0; color: var(--text-primary);">Set new password</h1>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-input label="Email address" name="email" type="email" value="{{ old('email', $request->email) }}" required readonly style="opacity: 0.7; cursor: not-allowed;" />

        <div style="position: relative;">
            <x-input label="New Password" name="password" type="password" required autocomplete="new-password" />
            <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';" style="position: absolute; right: 12px; top: 32px; width: 32px; height: 32px; background: transparent; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
        </div>

        <x-input label="Confirm New Password" name="password_confirmation" type="password" required autocomplete="new-password" />

        <x-button type="submit" variant="primary" style="width: 100%;">Set new password</x-button>
    </form>
</x-guest-layout>
