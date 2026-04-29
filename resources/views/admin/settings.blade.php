@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
    <div class="page-header" style="margin-bottom:32px;">
        <h1 class="text-page-title">System Settings</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert" style="margin-bottom:24px;">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 1.333A6.667 6.667 0 1 0 14.667 8 6.674 6.674 0 0 0 8 1.333Zm3.06 4.94-3.667 4a.667.667 0 0 1-.986 0L4.94 8.607a.667.667 0 1 1 .986-.9l.96 1.046 3.174-3.46a.667.667 0 0 1 .986.9l.014.02Z" fill="currentColor"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <div class="card" style="padding:32px; margin-bottom:24px;">
            <p class="text-card-title" style="margin-bottom:20px;">General</p>

            <div class="form-group">
                <label for="app_name" class="form-label">App Name</label>
                <input type="text" name="app_name" id="app_name" class="form-input" value="{{ old('app_name', $formData['app_name']) }}" required>
                @error('app_name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="app_url" class="form-label">App URL</label>
                <input type="url" name="app_url" id="app_url" class="form-input" value="{{ old('app_url', $formData['app_url']) }}" required>
                @error('app_url') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="support_email" class="form-label">Support Email</label>
                <input type="email" name="support_email" id="support_email" class="form-input" value="{{ old('support_email', $formData['support_email']) }}" required>
                @error('support_email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="card" style="padding:32px; margin-bottom:24px;">
            <p class="text-card-title" style="margin-bottom:20px;">Registration & Uploads</p>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="margin-bottom:8px;">Allow Public Registration</label>
                <label class="toggle-switch">
                    <input type="checkbox" name="allow_registration" {{ old('allow_registration', $formData['allow_registration']) ? 'checked' : '' }}>
                    <div class="toggle-track">
                        <div class="toggle-thumb"></div>
                    </div>
                </label>
            </div>
            
            <div class="form-group">
                <label for="max_upload_size" class="form-label">Max File Upload Size (MB)</label>
                <input type="number" name="max_upload_size" id="max_upload_size" class="form-input" value="{{ old('max_upload_size', $formData['max_upload_size']) }}" min="1" max="100" required style="width:160px;">
                @error('max_upload_size') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:8px;">Save All Settings</button>
    </form>
@endsection
