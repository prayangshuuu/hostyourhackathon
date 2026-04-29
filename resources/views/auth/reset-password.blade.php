<x-guest-layout>
    <x-slot name="title">Reset Password</x-slot>
    <x-slot name="metaDescription">Set a new password for your HostYourHackathon account.</x-slot>

    <x-slot name="heading">Reset your password</x-slot>
    <x-slot name="subheading">Choose a strong new password for your account.</x-slot>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5" id="reset-password-form">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
                class="mt-1.5 block w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-gray-500 shadow-sm transition duration-200 focus:border-indigo-500 focus:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                placeholder="you@example.com"
            />
            @error('email')
                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300">New password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="mt-1.5 block w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-gray-500 shadow-sm transition duration-200 focus:border-indigo-500 focus:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                placeholder="••••••••"
            />
            @error('password')
                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm new password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="mt-1.5 block w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-gray-500 shadow-sm transition duration-200 focus:border-indigo-500 focus:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                placeholder="••••••••"
            />
            @error('password_confirmation')
                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button
            type="submit"
            id="reset-password-submit"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:from-indigo-400 hover:to-purple-500 hover:shadow-indigo-500/40 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 active:scale-[0.98]"
        >
            Reset password
        </button>
    </form>

    <x-slot name="footer">
        <p class="text-sm text-gray-500">
            Remember your password?
            <a href="{{ route('login') }}" class="font-medium text-indigo-400 transition hover:text-indigo-300">Back to sign in</a>
        </p>
    </x-slot>
</x-guest-layout>
