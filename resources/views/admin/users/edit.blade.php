@extends('layouts.admin')

@section('title', 'Edit User Role')

@section('content')
    <div class="page-header">
        <h1 class="text-page-title">Edit User Role</h1>
    </div>

    <div style="max-width: 480px; margin: 0 auto;">
        <x-card>
            {{-- User info header --}}
            <div style="background: var(--surface-alt); padding: 16px; border-radius: var(--radius-lg); margin-bottom: 20px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--border); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; color: var(--text-secondary);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight: 500; font-size: 15px; color: var(--text-primary);">{{ $user->name }}</div>
                    <div style="font-size: 13px; color: var(--text-muted);">{{ $user->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 24px;">
                    <label class="form-label" for="role">Role</label>
                    <select name="role" id="role" class="form-input">
                        @foreach(\App\Enums\RoleEnum::cases() as $roleEnum)
                            <option value="{{ $roleEnum->value }}" {{ $user->hasRole($roleEnum->value) ? 'selected' : '' }}>
                                {{ ucfirst($roleEnum->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost" style="width: 100%; text-align: center; justify-content: center;">Cancel</a>
                </div>
            </form>
        </x-card>
    </div>
@endsection
