<header class="fixed {{ session('impersonating_user_id') ? 'top-9' : 'top-0' }} inset-x-0 z-40 h-14 bg-white border-b border-slate-200 flex items-center px-6">
    <div class="max-w-[1200px] mx-auto w-full flex items-center gap-12">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-[15px] font-bold text-slate-900 flex-shrink-0">
            <div class="w-7 h-7 rounded-md bg-accent-500 flex items-center justify-center">
                <x-heroicon-o-bolt class="w-4 h-4 text-white" />
            </div>
            <span>{{ $appSettings->get('app_name', 'HostYourHackathon') }}</span>
        </a>

        <nav class="hidden md:flex items-center gap-8">
            <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-accent-600 transition-colors">Home</a>
            @if($isSingleMode)
                <a href="{{ route('single.segments.index') }}" class="text-sm font-medium text-slate-600 hover:text-accent-600 transition-colors">Segments</a>
                <a href="{{ route('home') }}#about" class="text-sm font-medium text-slate-600 hover:text-accent-600 transition-colors">About</a>
            @else
                <a href="{{ route('hackathons.index') }}" class="text-sm font-medium text-slate-600 hover:text-accent-600 transition-colors">Browse Hackathons</a>
            @endif
        </nav>

        <div class="flex-1"></div>

        <div class="flex items-center gap-3">
            @auth
                <x-button :href="route('dashboard')" variant="secondary" size="sm" icon="home">Dashboard</x-button>
            @else
                <x-button :href="route('login')" variant="ghost" size="sm">Sign in</x-button>
                @if($isSingleMode && $singleHackathon && $singleHackathon->isRegistrationOpen())
                    <x-button :href="route('register')" variant="primary" size="sm">Register Now</x-button>
                @elseif(!$isSingleMode)
                    <x-button :href="route('register')" variant="primary" size="sm">Get Started</x-button>
                @endif
            @endauth
        </div>
    </div>
</header>
