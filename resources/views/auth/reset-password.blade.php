@extends('layouts.guest')
@section('title', 'Reset Password')

@slot('slot')
  <h1 class="text-xl font-bold text-slate-900 text-center">Reset password</h1>
  <p class="text-xs text-slate-500 text-center mt-1.5 mb-7">Enter your new password below.</p>

  <form method="POST" action="{{ route('password.store') }}" class="space-y-0">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    
    <x-input label="Email address" name="email" type="email" :value="old('email', $request->email)" :error="$errors->first('email')" required readonly />
    <x-input label="New password" name="password" type="password" :error="$errors->first('password')" required autocomplete="new-password" autofocus />
    <x-input label="Confirm password" name="password_confirmation" type="password" required autocomplete="new-password" />

    <div class="pt-2">
      <x-button type="submit" variant="primary" fullWidth size="lg">Reset password</x-button>
    </div>
  </form>
@endslot
