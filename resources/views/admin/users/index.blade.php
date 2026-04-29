@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <div class="page-header">
        <div class="page-header-row" style="flex-direction: column; align-items: flex-start; gap: 8px;">
            <h1 class="text-page-title" style="margin: 0;">Users</h1>
            <p class="text-helper" style="margin: 0;">{{ $total ?? $users->total() }} registered users</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; gap: 8px; margin-bottom: 24px;">
        <select name="role" class="form-input" style="width: 160px;" onchange="this.form.submit()">
            <option value="">All Roles</option>
            @foreach(\App\Enums\RoleEnum::cases() as $roleEnum)
                <option value="{{ $roleEnum->value }}" {{ request('role') == $roleEnum->value ? 'selected' : '' }}>
                    {{ ucfirst($roleEnum->value) }}
                </option>
            @endforeach
        </select>
        
        <div style="position: relative; width: 280px;">
            <input type="text" name="search" class="form-input" placeholder="Search by name or email" value="{{ request('search') }}" style="width: 100%; padding-right: 40px;">
            <button type="submit" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
        </div>
    </form>

    <x-card>
        <x-table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--surface-alt); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; color: var(--text-secondary);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--text-primary); font-size: 14px;">{{ $user->name }}</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $role = $user->roles->first()?->name ?? 'participant';
                                $variant = match($role) {
                                    'super_admin' => 'danger',
                                    'organizer' => 'indigo',
                                    'judge' => 'amber',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge :variant="$variant">{{ ucfirst($role) }}</x-badge>
                        </td>
                        <td style="font-size: 13px;">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($user->trashed())
                                <x-badge variant="danger">Deleted</x-badge>
                            @else
                                <x-badge variant="success">Active</x-badge>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                @if(!$user->trashed())
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost" style="padding: 6px;" title="Edit Role">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost" style="padding: 6px;" title="Impersonate">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                    </form>

                                    @if(Auth::id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-danger" style="padding: 6px;" title="Delete">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">Restore</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
        
        @if(method_exists($users, 'links'))
            <div style="padding: 16px; border-top: 1px solid var(--border-subtle);">
                <style>
                    nav[role="navigation"] { display: flex; align-items: center; justify-content: space-between; }
                    .pagination-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md); border: 1px solid var(--border); color: var(--text-primary); text-decoration: none; font-size: 14px; transition: background 150ms ease; }
                    .pagination-btn:hover { background: var(--surface-alt); }
                    .pagination-btn.active { background: var(--accent); color: white; border-color: var(--accent); }
                </style>
                {{ $users->links() }}
            </div>
        @endif
    </x-card>
@endsection
