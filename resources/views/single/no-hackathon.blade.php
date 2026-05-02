@extends('layouts.public')

@section('title', 'No Active Hackathon')

@section('content')
    <div style="min-height: 70vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 24px;">
        <div style="color: var(--text-muted); margin-bottom: 24px;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
                <line x1="9" y1="13" x2="15" y2="19"></line>
                <line x1="15" y1="13" x2="9" y2="19"></line>
            </svg>
        </div>
        
        <h1 style="font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0;">No Active Hackathon</h1>
        <p style="font-size: 16px; color: var(--text-secondary); margin-top: 8px; max-width: 400px;">
            There are no hackathons running right now. Check back soon for updates.
        </p>
        
        <div style="margin-top: 32px; display: flex; gap: 12px;">
            <a href="{{ route('home') }}" class="btn btn-secondary">Go Home</a>
        </div>
    </div>
@endsection
