@extends('layouts.guest')
@section('title', 'Sign in')

@slot('slot')
  <h1 class="text-xl font-bold text-slate-900 text-center">Welcome back</h1>
  <p class="text-xs text-slate-500 text-center mt-1.5 mb-7">Sign in to your account to continue</p>

  <form method="POST" action="{{ route('login') }}" class="space-y-0">
    @csrf
    <x-input label="Email address" name="email" type="email" :value="old('email')" :error="$errors->first('email')" required autofocus />
    
    <div class="mb-5">
      <div class="flex items-center justify-between mb-1.5">
        <label for="password" class="block text-2xs font-semibold text-slate-500 uppercase tracking-wide">Password <span class="text-red-500">*</span></label>
        <a href="{{ route('password.request') }}" class="text-2xs text-accent-600 hover:underline font-medium">Forgot password?</a>
      </div>
      <div class="relative" x-data="{ show: false }">
        <input :type="show ? 'text' : 'password'" id="password" name="password" class="form-input block w-full h-[36px] px-3 pr-10 text-sm text-slate-900 bg-white border {{ $errors->first('password') ? 'border-red-400' : 'border-slate-200 focus:border-accent-500' }} rounded-md focus:outline-none focus:ring-3 focus:ring-accent-500/15 transition-colors" required>
        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 w-9 flex items-center justify-center text-slate-400 hover:text-slate-600">
          <x-heroicon-o-eye x-show="!show" class="w-4 h-4" />
          <x-heroicon-o-eye-slash x-show="show" class="w-4 h-4" />
        </button>
      </div>
      @if($errors->first('password'))<p class="text-2xs text-red-500 mt-1">{{ $errors->first('password') }}</p>@endif
    </div>

    <div class="flex items-center gap-2 mb-5">
      <input type="checkbox" id="remember" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-accent-500 focus:ring-accent-500/20">
      <label for="remember" class="text-xs text-slate-600">Remember me</label>
    </div>

    <x-button type="submit" variant="primary" fullWidth size="lg">Sign in</x-button>
  </form>

  @if($appSettings->get('enable_google_oauth', true))
    <div class="relative my-6">
      <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
      <div class="relative flex justify-center"><span class="bg-white px-3 text-2xs text-slate-400 font-medium">or</span></div>
    </div>
    <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-2.5 h-[36px] w-full text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-md hover:bg-slate-50 hover:border-slate-300 transition-colors">
      <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
      Continue with Google
    </a>
  @endif

  <p class="text-center text-xs text-slate-500 mt-6">
    Don't have an account?
    @if($appSettings->get('allow_registration', true))
      <a href="{{ route('register') }}" class="text-accent-600 font-semibold hover:underline">Sign up</a>
    @else
      <span class="text-slate-400">Registration is closed</span>
    @endif
  </p>
@endslot
