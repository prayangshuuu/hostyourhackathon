<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $appSettings->get('app_name', 'HostYourHackathon') }} — {{ $title ?? 'Sign in' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#f7f9fb] font-sans antialiased flex items-center justify-center p-4 min-h-screen">
  <div class="w-full max-w-[420px]">
    <div class="text-center mb-8">
      <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-[15px] font-bold text-slate-900">
        <div class="w-8 h-8 rounded-lg bg-accent-500 flex items-center justify-center">
          <x-heroicon-o-bolt class="w-4 h-4 text-white" />
        </div>
        {{ $appSettings->get('app_name', 'HostYourHackathon') }}
      </a>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
      @if(session('success'))<x-alert type="success">{{ session('success') }}</x-alert>@endif
      @if(session('error'))<x-alert type="danger">{{ session('error') }}</x-alert>@endif
      {{ $slot }}
    </div>
    <p class="text-center text-2xs text-slate-400 mt-6">© {{ date('Y') }} {{ $appSettings->get('app_name', 'HostYourHackathon') }}. All rights reserved.</p>
  </div>
</body>
</html>
