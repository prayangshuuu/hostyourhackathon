@extends('layouts.guest')
@section('title', 'Forgot Password')

@slot('slot')
  <h1 class="text-xl font-bold text-slate-900 text-center">Forgot password?</h1>
  <p class="text-xs text-slate-500 text-center mt-1.5 mb-7">No worries, we'll send you reset instructions.</p>

  @if (session('status'))
    <x-alert type="success">{{ session('status') }}</x-alert>
  @endif

  <form method="POST" action="{{ route('password.email') }}" class="space-y-0">
    @csrf
    <x-input label="Email address" name="email" type="email" :value="old('email')" :error="$errors->first('email')" required autofocus />

    <div class="pt-2">
      <x-button type="submit" variant="primary" fullWidth size="lg">Send reset link</x-button>
    </div>
  </form>

  <p class="text-center text-xs text-slate-500 mt-6">
    Remember your password?
    <a href="{{ route('login') }}" class="text-accent-600 font-semibold hover:underline">Sign in</a>
  </p>
@endslot
