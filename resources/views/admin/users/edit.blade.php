@extends('layouts.app')

@section('title', 'Edit User Role')

@section('content')
    <div class="page-header" style="margin-bottom:24px;">
        <div class="page-header-breadcrumb">
            <a href="{{ route('admin.users.index') }}">Users</a>
            <span class="separator">/</span>
            <span>Edit Role</span>
        </div>
        <h1 class="text-page-title">Edit User Role</h1>
    </div>

    <div style="max-width:480px; margin:0 auto;">
        <div class="card" style="padding:32px;">
            {{-- User Info (read-only) --}}
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid var(--color-border-subtle);">
                <div class="user-list-avatar" style="width:48px; height:48px; font-size:var(--font-size-lg);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:var(--font-weight-semibold); color:var(--color-text-primary);">{{ $user->name }}</div>
                    <div style="font-size:var(--font-size-sm); color:var(--color-text-muted);">{{ $user->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach ($roles as $role)
                            @php $currentRole = $user->roles->first()?->name; @endphp
                            <option value="{{ $role }}" {{ $currentRole === $role ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="display:flex; gap:8px; margin-top:24px;">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
