@extends('layouts.app')

@section('title', 'My Hackathons')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 class="text-page-title">My Hackathons</h1>
        <x-button href="{{ route('organizer.hackathons.create') }}" variant="primary">New Hackathon</x-button>
    </div>

    @if (session('success'))
        <div style="margin-bottom: 24px;">
            <x-alert type="success" :message="session('success')" />
        </div>
    @endif
    @if (session('error'))
        <div style="margin-bottom: 24px;">
            <x-alert type="error" :message="session('error')" />
        </div>
    @endif

    <div class="card">
        @if ($hackathons->isEmpty())
            <div style="text-align: center; padding: 48px 24px;">
                <p style="color: var(--text-secondary); margin-bottom: 16px;">You haven't created any hackathons yet.</p>
                <x-button href="{{ route('organizer.hackathons.create') }}" variant="primary">Create Your First Hackathon</x-button>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th style="text-align: left; padding: 16px; font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Hackathon</th>
                            <th style="text-align: left; padding: 16px; font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Status</th>
                            <th style="text-align: left; padding: 16px; font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Teams</th>
                            <th style="text-align: left; padding: 16px; font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Submissions</th>
                            <th style="text-align: left; padding: 16px; font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Registration Closes</th>
                            <th style="text-align: right; padding: 16px; font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hackathons as $hackathon)
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 16px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        @if ($hackathon->logo)
                                            <img src="{{ Storage::url($hackathon->logo) }}" alt="{{ $hackathon->title }}" style="width: 32px; height: 32px; border-radius: var(--radius-sm); border: 1px solid var(--border); object-fit: cover;">
                                        @else
                                            <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface-alt); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: var(--text-muted);">
                                                {{ strtoupper(substr($hackathon->title, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div style="font-weight: 500; color: var(--text-primary);">{{ $hackathon->title }}</div>
                                    </div>
                                </td>
                                <td style="padding: 16px;">
                                    @php
                                        $variant = match($hackathon->status->value) {
                                            'published' => 'primary',
                                            'ongoing' => 'success',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <x-badge :variant="$variant">{{ ucfirst($hackathon->status->value) }}</x-badge>
                                </td>
                                <td style="padding: 16px; color: var(--text-secondary);">{{ $hackathon->teams_count }}</td>
                                <td style="padding: 16px; color: var(--text-secondary);">{{ $hackathon->submissions_count }}</td>
                                <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">
                                    {{ $hackathon->registration_closes_at ? $hackathon->registration_closes_at->format('M j, Y') : '—' }}
                                </td>
                                <td style="padding: 16px; text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                        <a href="{{ route('organizer.hackathons.show', $hackathon) }}" style="color: var(--accent); font-size: 14px; font-weight: 500; text-decoration: none;">Manage</a>
                                        
                                        <a href="{{ route('organizer.hackathons.edit', $hackathon) }}" style="color: var(--text-secondary);" title="Edit">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                                        </a>

                                        <form method="POST" action="{{ route('organizer.hackathons.destroy', $hackathon) }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this hackathon? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; padding: 0; color: var(--danger); cursor: pointer;" title="Delete">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
