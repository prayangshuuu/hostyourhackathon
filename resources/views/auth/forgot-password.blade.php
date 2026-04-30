<x-guest-layout>
    <x-slot name="title">Forgot Password</x-slot>
    <x-slot name="metaDescription">Reset your {{ $appSettings->get('app_name', config('app.name')) }} password.</x-slot>

    <div style="text-align: center; margin-bottom: 24px;">
        <a href="{{ route('home') }}" style="display: inline-block; font-size: 24px; font-weight: 700; color: var(--text-primary); text-decoration: none;">
            {{ $appSettings->get('app_name', config('app.name')) }}
        </a>
    </div>

    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 20px; font-weight: 600; margin: 0 0 4px 0; color: var(--text-primary);">Reset password</h1>
        <p style="font-size: 14px; color: var(--text-muted); margin: 0;">Enter your email and we'll send you a reset link</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <x-input label="Email address" name="email" type="email" value="{{ old('email') }}" required autofocus />

        <x-button type="submit" variant="primary" style="width: 100%;">Send reset link</x-button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-secondary);">
        <a href="{{ route('login') }}" style="color: var(--accent); text-decoration: none;">Back to login</a>
    </div>
</x-guest-layout>
