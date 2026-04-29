<x-guest-layout>
    <x-slot name="title">Verify Email</x-slot>
    <x-slot name="metaDescription">Verify your email address to complete your HostYourHackathon registration.</x-slot>

    <x-slot name="heading">Verify your email</x-slot>
    <x-slot name="subheading">We've sent a verification link to your email address. Click it to activate your account.</x-slot>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-400">
            A new verification link has been sent to <span class="font-semibold">{{ auth()->user()->email }}</span>.
        </div>
    @endif

    <div class="rounded-lg border border-amber-500/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
        <div class="flex items-start gap-3">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
            <p>Check your inbox and spam folder. Didn't receive it? Click below to resend.</p>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}" id="resend-verification-form">
            @csrf
            <button
                type="submit"
                id="resend-verification-submit"
                class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:from-indigo-400 hover:to-purple-500 hover:shadow-indigo-500/40 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 active:scale-[0.98]"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" />
                </svg>
                Resend email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" id="verify-email-logout-form">
            @csrf
            <button
                type="submit"
                id="verify-email-logout-submit"
                class="rounded-lg border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-medium text-gray-400 transition-all duration-200 hover:border-white/20 hover:bg-white/10 hover:text-white"
            >
                Log out
            </button>
        </form>
    </div>
</x-guest-layout>
