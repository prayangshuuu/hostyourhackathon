<x-guest-layout>
    <x-slot name="title">Verify Email</x-slot>
    <x-slot name="metaDescription">Verify your email address for HostYourHackathon.</x-slot>

    <div style="text-align: center; margin-bottom: 24px;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: var(--surface-alt); margin-bottom: 16px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
        </div>
        <h1 style="font-size: 20px; font-weight: 600; margin: 0 0 8px 0; color: var(--text-primary);">Check your email</h1>
        <p style="font-size: 14px; color: var(--text-muted); margin: 0;">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div style="margin-bottom: 20px; font-size: 14px; color: var(--success); text-align: center; background: rgba(34, 197, 94, 0.1); padding: 12px; border-radius: var(--radius-md);">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 16px;">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button type="submit" variant="secondary" style="width: 100%;">
                Resend verification email
            </x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="text-align: center;">
            @csrf
            <button type="submit" style="background: transparent; border: none; font-size: 14px; color: var(--text-secondary); cursor: pointer; padding: 0;">
                Log out
            </button>
        </form>
    </div>
</x-guest-layout>
