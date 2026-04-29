<x-guest-layout>
    <x-slot name="title">Forgot Password</x-slot>
    <x-slot name="metaDescription">Reset your HostYourHackathon password by requesting a secure reset link.</x-slot>

    <x-slot name="heading">Forgot your password?</x-slot>
    <x-slot name="subheading">No worries — enter your email and we'll send you a reset link.</x-slot>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5" id="forgot-password-form">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="mt-1.5 block w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-gray-500 shadow-sm transition duration-200 focus:border-indigo-500 focus:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                placeholder="you@example.com"
            />
            @error('email')
                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button
            type="submit"
            id="forgot-password-submit"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:from-indigo-400 hover:to-purple-500 hover:shadow-indigo-500/40 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 active:scale-[0.98]"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
            Send reset link
        </button>
    </form>

    <x-slot name="footer">
        <p class="text-sm text-gray-500">
            Remember your password?
            <a href="{{ route('login') }}" class="font-medium text-indigo-400 transition hover:text-indigo-300">Back to sign in</a>
        </p>
    </x-slot>
</x-guest-layout>
