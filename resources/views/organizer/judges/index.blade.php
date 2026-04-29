@extends('layouts.app')

@section('title', 'Judges')

@section('content')
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('organizer.hackathons.index') }}">My Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ $hackathon->title }}</a>
            <span class="separator">/</span>
            <span>Judges</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Judges</h1>
        </div>
    </div>

    <x-alert />

    <div class="content-grid-8-4">
        {{-- Left: Assigned Judges --}}
        <x-card title="Assigned Judges">
            <x-table>
                <thead>
                    <tr>
                        <th>Judge</th>
                        <th>Email</th>
                        <th>Assigned Segment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($judges as $judge)
                        <tr>
                            <td>{{ $judge->name }}</td>
                            <td>{{ $judge->email }}</td>
                            <td>
                                @if($judge->pivot->segment_id)
                                    <x-badge variant="indigo">{{ $hackathon->segments->find($judge->pivot->segment_id)?->name ?? 'Unknown Segment' }}</x-badge>
                                @else
                                    <x-badge variant="neutral">All Segments</x-badge>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('organizer.judges.destroy', [$hackathon->id, $judge->id]) }}" method="POST" onsubmit="return confirm('Remove this judge?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-danger" style="padding: 8px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                No judges assigned yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </x-card>

        {{-- Right: Add Judge --}}
        <div>
            <x-card title="Assign Judge">
                <form method="POST" action="{{ route('organizer.judges.store', $hackathon->id) }}">
                    @csrf
                    
                    <div style="margin-bottom: 16px;">
                        <label class="form-label" for="email">User Email</label>
                        <input type="email" name="email" id="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label class="form-label" for="segment_id">Assign to Segment</label>
                        <select name="segment_id" id="segment_id" class="form-input">
                            <option value="">All Segments</option>
                            @foreach($hackathon->segments as $segment)
                                <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
                                    {{ $segment->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Assign Judge</button>
                </form>
            </x-card>
        </div>
    </div>
@endsection
