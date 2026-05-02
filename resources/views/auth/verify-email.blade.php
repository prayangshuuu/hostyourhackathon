@extends('layouts.guest')
@section('title', 'Verify Email')

@slot('slot')
  <h1 class="text-xl font-bold text-slate-900 text-center">Verify email</h1>
  <p class="text-xs text-slate-500 text-center mt-1.5 mb-7">
    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
  </p>

  @if (session('status') == 'verification-link-sent')
    <x-alert type="success">
      A new verification link has been sent to the email address you provided during registration.
    </x-alert>
  @endif

  <div class="flex flex-col gap-3">
    <form method="POST" action="{{ route('verification.send') }}">
      @csrf
      <x-button type="submit" variant="primary" fullWidth size="lg">Resend verification email</x-button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <x-button type="submit" variant="ghost" fullWidth size="sm">Sign out</x-button>
    </form>
  </div>
@endslot
