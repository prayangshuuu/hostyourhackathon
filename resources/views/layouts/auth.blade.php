<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'HostYourHackathon') }} — {{ config('app.name', 'HostYourHackathon') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Host, manage, and participate in hackathons with HostYourHackathon.' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-950 text-gray-100">
        {{-- Animated gradient background --}}
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-40 -left-40 h-[600px] w-[600px] rounded-full bg-indigo-600/20 blur-[128px] animate-pulse"></div>
            <div class="absolute -bottom-40 -right-40 h-[500px] w-[500px] rounded-full bg-purple-600/20 blur-[128px] animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[400px] w-[400px] rounded-full bg-cyan-500/10 blur-[100px] animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8 sm:py-12">
            {{-- Logo --}}
            <a href="/" class="mb-8 flex items-center gap-3 group">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/25 transition-transform duration-300 group-hover:scale-110">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                    {{ config('app.name', 'HostYourHackathon') }}
                </span>
            </a>

            {{-- Auth card with glassmorphism --}}
            <div class="w-full sm:max-w-md">
                <div class="rounded-2xl border border-white/10 bg-gray-900/70 px-6 py-8 shadow-2xl shadow-black/20 backdrop-blur-xl sm:px-10">
                    {{-- Page heading --}}
                    @isset($heading)
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold tracking-tight text-white">{{ $heading }}</h1>
                            @isset($subheading)
                                <p class="mt-2 text-sm text-gray-400">{{ $subheading }}</p>
                            @endisset
                        </div>
                    @endisset

                    {{-- Session status --}}
                    @if (session('status'))
                        <div class="mb-4 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Success message --}}
                    @if (session('success'))
                        <div class="mb-4 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>

                {{-- Footer link slot --}}
                @isset($footer)
                    <div class="mt-6 text-center">
                        {{ $footer }}
                    </div>
                @endisset
            </div>

            {{-- Attribution --}}
            <p class="mt-10 text-center text-xs text-gray-600">
                &copy; {{ date('Y') }} {{ config('app.name', 'HostYourHackathon') }}. All rights reserved.
            </p>
        </div>
    </body>
</html>
