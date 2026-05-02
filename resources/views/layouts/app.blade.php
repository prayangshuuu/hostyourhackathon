<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $appSettings->get('app_name', 'HostYourHackathon') }} — {{ $title ?? 'Dashboard' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#f7f9fb] font-sans antialiased">

  {{-- IMPERSONATION BANNER --}}
  @if(session('impersonating_user_id'))
    <div class="fixed top-0 inset-x-0 z-[200] h-9 bg-amber-400 flex items-center justify-between px-5">
      <div class="flex items-center gap-2 text-amber-900 text-xs font-semibold">
        <x-heroicon-o-eye class="w-3.5 h-3.5" />
        Impersonating: {{ auth()->user()->name }} ({{ auth()->user()->email }})
      </div>
      <a href="{{ route('admin.impersonate.exit') }}" class="text-xs font-semibold text-amber-900 underline hover:no-underline">Exit</a>
    </div>
  @endif

  {{-- TOP NAV --}}
  <header class="fixed {{ session('impersonating_user_id') ? 'top-9' : 'top-0' }} inset-x-0 z-40 h-14 bg-white border-b border-slate-200 flex items-center px-5 gap-4">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 text-[15px] font-bold text-slate-900 flex-shrink-0">
      <div class="w-7 h-7 rounded-md bg-accent-500 flex items-center justify-center">
        <x-heroicon-o-bolt class="w-4 h-4 text-white" />
      </div>
      <span>{{ $appSettings->get('app_name', 'HostYourHackathon') }}</span>
    </a>

    <div class="flex-1"></div>

    {{-- Notification Bell --}}
    <div class="relative" x-data="{ open: false }">
      <button @click="open = !open" class="relative w-9 h-9 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
        <x-heroicon-o-bell class="w-5 h-5" />
        @if($unreadCount > 0)
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-accent-500 rounded-full ring-2 ring-white"></span>
        @endif
      </button>
      <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 top-full mt-2 w-80 bg-white border border-slate-200 rounded-xl overflow-hidden z-50 shadow-xl">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
          <span class="text-xs font-semibold text-slate-700">Notifications</span>
          @if($unreadCount > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">@csrf
              <button type="submit" class="text-2xs text-accent-600 font-semibold hover:underline">Mark all read</button>
            </form>
          @endif
        </div>
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
          @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notif)
            <div class="px-4 py-3 {{ $notif->read_at ? '' : 'bg-accent-50 border-l-2 border-accent-400' }}">
              <p class="text-xs text-slate-800 leading-relaxed">{{ $notif->data['message'] ?? '' }}</p>
              <p class="text-2xs text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
          @empty
            <div class="px-4 py-8 text-center text-xs text-slate-400">No notifications</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- User Menu --}}
    <div class="relative" x-data="{ open: false }">
      <button @click="open = !open" class="flex items-center gap-2 h-9 px-2 rounded-lg hover:bg-slate-100 transition-colors">
        <div class="w-7 h-7 rounded-full bg-accent-50 text-accent-600 flex items-center justify-center text-[11px] font-bold">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <span class="text-xs font-medium text-slate-700 hidden sm:block max-w-[120px] truncate">{{ auth()->user()->name }}</span>
        <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-slate-400" />
      </button>
      <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 top-full mt-2 w-52 bg-white border border-slate-200 rounded-xl overflow-hidden z-50 py-1 shadow-xl">
        <div class="px-3 py-2.5 border-b border-slate-100">
          <p class="text-xs font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
          <p class="text-2xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
        </div>
        <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors">
          <x-heroicon-o-user-circle class="w-4 h-4 text-slate-400" /> Profile
        </a>
        @if(auth()->user()->hasRole('super_admin'))
          <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors">
            <x-heroicon-o-cog-6-tooth class="w-4 h-4 text-slate-400" /> Admin Panel
          </a>
        @endif
        <div class="border-t border-slate-100 mt-1 pt-1">
          <form action="{{ route('logout') }}" method="POST">@csrf
            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors">
              <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" /> Sign out
            </button>
          </form>
        </div>
      </div>
    </div>
  </header>

  <div class="flex {{ session('impersonating_user_id') ? 'pt-[92px]' : 'pt-14' }}">
    {{-- SIDEBAR --}}
    <aside class="fixed {{ session('impersonating_user_id') ? 'top-[92px]' : 'top-14' }} left-0 w-60 h-[calc(100vh-56px)] bg-white border-r border-slate-200 overflow-y-auto z-30 flex flex-col">
      <nav class="flex-1 p-3">
        {{-- Single mode hackathon chip --}}
        @if($isSingleMode)
          <div class="mb-3 px-2">
            @if($singleHackathon)
              <div class="bg-accent-50 border border-accent-100 rounded-lg px-3 py-2.5">
                <p class="text-2xs font-semibold text-accent-500 uppercase tracking-wide">Active Hackathon</p>
                <p class="text-xs font-semibold text-slate-900 mt-0.5 line-clamp-2">{{ $singleHackathon->title }}</p>
                <x-badge variant="success" dot class="mt-1.5">{{ ucfirst($singleHackathon->status->value) }}</x-badge>
              </div>
            @else
              <div class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5">
                <p class="text-xs text-slate-400 text-center">No active hackathon</p>
              </div>
            @endif
          </div>
        @endif

        {{-- Nav items per role --}}
        @include('partials.sidebar-nav')
      </nav>

      {{-- Bottom: role badge --}}
      <div class="p-3 border-t border-slate-100">
        <div class="flex items-center gap-2.5 px-2 py-2">
          <div class="w-7 h-7 rounded-full bg-accent-50 text-accent-600 flex items-center justify-center text-[11px] font-bold flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-800 truncate">{{ auth()->user()->name }}</p>
            <x-badge variant="{{ match(auth()->user()->roles->first()?->name) { 'super_admin'=>'danger','organizer'=>'violet','judge'=>'amber','mentor'=>'teal',default=>'neutral' } }}">
              {{ str_replace('_',' ',ucfirst(auth()->user()->roles->first()?->name ?? 'user')) }}
            </x-badge>
          </div>
        </div>
      </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="ml-60 flex-1 min-h-[calc(100vh-56px)] p-8">
      @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
      @endif
      @if(session('error'))
        <x-alert type="danger">{{ session('error') }}</x-alert>
      @endif
      {{ $slot }}
    </main>
  </div>

</body>
</html>
