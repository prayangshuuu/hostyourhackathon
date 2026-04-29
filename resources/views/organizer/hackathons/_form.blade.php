{{-- Shared hackathon form partial — used by create.blade.php and edit.blade.php --}}
@php $isEdit = isset($hackathon) && $hackathon->exists; @endphp

{{-- Basic Information --}}
<div class="card section-spacing">
    <div class="card-header">
        <h2 class="text-card-title">Basic Information</h2>
    </div>
    <div class="form-grid-2">
        <div class="form-group">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title"
                   class="form-input @error('title') is-invalid @enderror"
                   value="{{ old('title', $isEdit ? $hackathon->title : '') }}"
                   placeholder="e.g. Global AI Hackathon 2026" required>
            @error('title') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-group">
            <label for="tagline" class="form-label">Tagline</label>
            <input type="text" name="tagline" id="tagline"
                   class="form-input @error('tagline') is-invalid @enderror"
                   value="{{ old('tagline', $isEdit ? $hackathon->tagline : '') }}"
                   placeholder="A short catchy line" required>
            @error('tagline') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    @if ($isEdit)
        <div class="form-group" style="margin-top:4px;">
            <label class="form-label">Slug</label>
            <div style="padding:10px 14px; background:var(--color-bg); border:1px solid var(--color-border-subtle); border-radius:var(--radius-md); color:var(--color-text-muted); font-size:var(--font-size-sm);">
                {{ $hackathon->slug }}
            </div>
            <p class="form-helper">Slug cannot be changed after creation.</p>
        </div>
    @endif

    <div class="form-group">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description"
                  class="form-textarea @error('description') is-invalid @enderror"
                  placeholder="Describe your hackathon in detail…" required>{{ old('description', $isEdit ? $hackathon->description : '') }}</textarea>
        @error('description') <p class="form-error">{{ $message }}</p> @enderror
        <p class="form-helper">Supports plain text. Rich text editor coming soon.</p>
    </div>
</div>

{{-- Branding --}}
<div class="card section-spacing">
    <div class="card-header">
        <h2 class="text-card-title">Branding</h2>
    </div>
    <div class="form-grid-2">
        <div class="form-group">
            <label for="logo" class="form-label">Logo</label>
            @if ($isEdit && $hackathon->logo)
                <div style="margin-bottom:8px;">
                    <img src="{{ Storage::url($hackathon->logo) }}" alt="Current logo"
                         style="height:48px; border-radius:var(--radius-sm); border:1px solid var(--color-border);">
                </div>
            @endif
            <input type="file" name="logo" id="logo"
                   class="form-input @error('logo') is-invalid @enderror"
                   accept="image/*">
            @error('logo') <p class="form-error">{{ $message }}</p> @enderror
            <p class="form-helper">Max 2 MB. JPG, PNG, SVG, or WebP.</p>
        </div>
        <div class="form-group">
            <label for="banner" class="form-label">Banner</label>
            @if ($isEdit && $hackathon->banner)
                <div style="margin-bottom:8px;">
                    <img src="{{ Storage::url($hackathon->banner) }}" alt="Current banner"
                         style="height:48px; border-radius:var(--radius-sm); border:1px solid var(--color-border);">
                </div>
            @endif
            <input type="file" name="banner" id="banner"
                   class="form-input @error('banner') is-invalid @enderror"
                   accept="image/*">
            @error('banner') <p class="form-error">{{ $message }}</p> @enderror
            <p class="form-helper">Max 4 MB. Recommended 1200×400px.</p>
        </div>
    </div>
    <div class="form-group" style="max-width:200px;">
        <label for="primary_color" class="form-label">Primary Color</label>
        <div style="display:flex; align-items:center; gap:10px;">
            <input type="color" name="primary_color" id="primary_color"
                   value="{{ old('primary_color', $isEdit ? $hackathon->primary_color : '#6366f1') }}"
                   style="width:40px; height:36px; padding:2px; border:1px solid var(--color-border); border-radius:var(--radius-sm); background:var(--color-surface-raised); cursor:pointer;">
            <input type="text" id="primary_color_hex"
                   class="form-input" style="width:100px;"
                   value="{{ old('primary_color', $isEdit ? $hackathon->primary_color : '#6366f1') }}"
                   pattern="^#[0-9A-Fa-f]{6}$" maxlength="7" readonly>
        </div>
        @error('primary_color') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Team Settings --}}
<div class="card section-spacing">
    <div class="card-header">
        <h2 class="text-card-title">Team Settings</h2>
    </div>
    <div class="form-grid-2">
        <div class="form-group">
            <label for="min_team_size" class="form-label">Min Team Size</label>
            <input type="number" name="min_team_size" id="min_team_size"
                   class="form-input @error('min_team_size') is-invalid @enderror"
                   value="{{ old('min_team_size', $isEdit ? $hackathon->min_team_size : 1) }}"
                   min="1" required>
            @error('min_team_size') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-group">
            <label for="max_team_size" class="form-label">Max Team Size</label>
            <input type="number" name="max_team_size" id="max_team_size"
                   class="form-input @error('max_team_size') is-invalid @enderror"
                   value="{{ old('max_team_size', $isEdit ? $hackathon->max_team_size : 5) }}"
                   min="1" required>
            @error('max_team_size') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="form-group">
        <div class="toggle-wrapper">
            <label class="toggle-switch">
                <input type="hidden" name="allow_solo" value="0">
                <input type="checkbox" name="allow_solo" id="allow_solo" value="1"
                       {{ old('allow_solo', $isEdit ? $hackathon->allow_solo : true) ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
            <label for="allow_solo" class="form-label" style="margin-bottom:0; cursor:pointer;">Allow Solo Participants</label>
        </div>
        @error('allow_solo') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Schedule --}}
<div class="card section-spacing">
    <div class="card-header">
        <h2 class="text-card-title">Schedule</h2>
    </div>
    <div class="form-grid-2">
        <div class="form-group">
            <label for="registration_opens_at" class="form-label">Registration Opens</label>
            <input type="datetime-local" name="registration_opens_at" id="registration_opens_at"
                   class="form-input @error('registration_opens_at') is-invalid @enderror"
                   value="{{ old('registration_opens_at', $isEdit && $hackathon->registration_opens_at ? $hackathon->registration_opens_at->format('Y-m-d\TH:i') : '') }}">
            @error('registration_opens_at') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-group">
            <label for="registration_closes_at" class="form-label">Registration Closes</label>
            <input type="datetime-local" name="registration_closes_at" id="registration_closes_at"
                   class="form-input @error('registration_closes_at') is-invalid @enderror"
                   value="{{ old('registration_closes_at', $isEdit && $hackathon->registration_closes_at ? $hackathon->registration_closes_at->format('Y-m-d\TH:i') : '') }}">
            @error('registration_closes_at') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-group">
            <label for="submission_opens_at" class="form-label">Submission Opens</label>
            <input type="datetime-local" name="submission_opens_at" id="submission_opens_at"
                   class="form-input @error('submission_opens_at') is-invalid @enderror"
                   value="{{ old('submission_opens_at', $isEdit && $hackathon->submission_opens_at ? $hackathon->submission_opens_at->format('Y-m-d\TH:i') : '') }}">
            @error('submission_opens_at') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-group">
            <label for="submission_closes_at" class="form-label">Submission Closes</label>
            <input type="datetime-local" name="submission_closes_at" id="submission_closes_at"
                   class="form-input @error('submission_closes_at') is-invalid @enderror"
                   value="{{ old('submission_closes_at', $isEdit && $hackathon->submission_closes_at ? $hackathon->submission_closes_at->format('Y-m-d\TH:i') : '') }}">
            @error('submission_closes_at') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="form-group" style="max-width:calc(50% - 10px);">
        <label for="results_at" class="form-label">Results Announcement</label>
        <input type="datetime-local" name="results_at" id="results_at"
               class="form-input @error('results_at') is-invalid @enderror"
               value="{{ old('results_at', $isEdit && $hackathon->results_at ? $hackathon->results_at->format('Y-m-d\TH:i') : '') }}">
        @error('results_at') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Color picker sync --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const picker = document.getElementById('primary_color');
        const hex = document.getElementById('primary_color_hex');
        if (picker && hex) {
            picker.addEventListener('input', function () { hex.value = picker.value; });
        }
    });
</script>
@endpush
