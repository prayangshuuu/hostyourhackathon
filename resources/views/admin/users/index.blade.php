@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="page-header" style="margin-bottom:16px;">
        <h1 class="text-page-title">Users</h1>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.users.index') }}" style="margin-bottom:24px;">
        <div style="display:flex; gap:8px; align-items:center;">
            <select name="role" class="form-select" style="width:160px;">
                <option value="">All Roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-input" placeholder="Search by email or name…" value="{{ request('search') }}" style="width:220px;">
            <input type="date" name="from" class="form-input" value="{{ request('from') }}" style="width:140px;" placeholder="From">
            <input type="date" name="to" class="form-input" value="{{ request('to') }}" style="width:140px;" placeholder="To">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if (request()->hasAny(['role', 'search', 'from', 'to']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Clear</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="ds-table-wrapper">
        <table class="ds-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th style="width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @php $role = $user->roles->first()?->name ?? 'participant'; @endphp
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div class="user-list-avatar" style="width:28px; height:28px; font-size:11px;">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <span style="font-weight:var(--font-weight-medium);">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--color-text-muted);">{{ $user->email }}</td>
                        <td><span class="badge badge-role-{{ $role }}">{{ $role }}</span></td>
                        <td style="font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            @if ($user->trashed())
                                <span class="badge" style="background:var(--color-danger-subtle); color:var(--color-danger); border:1px solid rgba(239,68,68,0.2);">Deleted</span>
                            @else
                                <span class="badge badge-scored">Active</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:4px;">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon" title="Edit Role">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M11.333 2A1.886 1.886 0 0 1 14 4.667l-8.667 8.666L2 14l.667-3.333 8.666-8.667Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                                @if (!$user->trashed() && $user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn-icon" title="Impersonate">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M1.333 8s2.667-5.333 6.667-5.333S14.667 8 14.667 8s-2.667 5.333-6.667 5.333S1.333 8 1.333 8Z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.2"/></svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="margin:0;" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon" title="Delete" style="color:var(--color-danger);">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination" style="margin-top: 24px;">
        {{ $users->appends(request()->query())->links() }}
    </div>
@endsection
