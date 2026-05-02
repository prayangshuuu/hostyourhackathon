<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $appSettings->get('app_name', 'HostYourHackathon') }} — {{ $title ?? 'Home' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#f7f9fb] font-sans antialiased">

  @if(session('impersonating_user_id'))
    <div class="fixed top-0 inset-x-0 z-[200] h-9 bg-amber-400 flex items-center justify-between px-5">
      <div class="flex items-center gap-2 text-amber-900 text-xs font-semibold">
        <x-heroicon-o-eye class="w-3.5 h-3.5" />
        Impersonating: {{ auth()->user()->name }}
      </div>
      <a href="{{ route('admin.impersonate.exit') }}" class="text-xs font-semibold text-amber-900 underline hover:no-underline">Exit</a>
    </div>
  @endif

  @include('partials.public-nav')

  <main class="{{ session('impersonating_user_id') ? 'pt-[92px]' : 'pt-14' }} min-h-screen">
    @yield('content')
    {{ $slot ?? '' }}
  </main>

  @stack('scripts')
</body>
</html>
