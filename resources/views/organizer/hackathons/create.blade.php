@extends('layouts.app')

@section('title', 'New Hackathon')

@section('content')
    <div style="margin-bottom: 24px;">
        <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
            <a href="{{ route('organizer.hackathons.index') }}" style="color: inherit; text-decoration: none;">My Hackathons</a> / New Hackathon
        </div>
        <h1 class="text-page-title">New Hackathon</h1>
    </div>

    @if ($errors->any())
        <div style="margin-bottom: 24px;">
            <x-alert type="error" message="Please check the form below for errors." />
        </div>
    @endif

    <form id="hackathon-form" method="POST" action="{{ route('organizer.hackathons.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 24px;">
            
            {{-- Left Column --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                {{-- Basic Information --}}
                <div class="card" style="padding: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Basic Information</h2>
                    
                    <div style="margin-bottom: 16px;">
                        <x-input type="text" name="title" id="title" label="Hackathon Title" :value="old('title')" required onkeyup="updateSlugPreview(this.value)" />
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                            URL: /h/<span id="slug-preview">auto-generated-slug</span>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <x-input type="text" name="tagline" label="Tagline (optional)" :value="old('tagline')" />
                    </div>

                    <div>
                        <label for="description" class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Description</label>
                        <textarea name="description" id="description" class="form-input" style="width: 100%; min-height: 160px; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius-md);" required>{{ old('description') }}</textarea>
                        @error('description') <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Branding --}}
                <div class="card" style="padding: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Branding</h2>
                    
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <label class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Logo</label>
                            <div style="position: relative; width: 120px; height: 120px;">
                                <div id="logo-drop-area" style="width: 100%; height: 100%; border: 2px dashed var(--border); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; background: var(--surface-alt); cursor: pointer;" onclick="document.getElementById('logo-input').click()">
                                    <span style="font-size: 13px; color: var(--text-muted);">Upload Logo</span>
                                </div>
                                <img id="logo-preview" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg); display: none; cursor: pointer;" onclick="document.getElementById('logo-input').click()">
                                <input type="file" name="logo" id="logo-input" accept="image/*" style="display: none;" onchange="previewImage(this, 'logo-preview', 'logo-drop-area')">
                            </div>
                            @error('logo') <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Banner</label>
                            <div style="position: relative; width: 100%; height: 140px;">
                                <div id="banner-drop-area" style="width: 100%; height: 100%; border: 2px dashed var(--border); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; background: var(--surface-alt); cursor: pointer;" onclick="document.getElementById('banner-input').click()">
                                    <span style="font-size: 13px; color: var(--text-muted);">Upload Banner</span>
                                </div>
                                <img id="banner-preview" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg); display: none; cursor: pointer;" onclick="document.getElementById('banner-input').click()">
                                <input type="file" name="banner" id="banner-input" accept="image/*" style="display: none;" onchange="previewImage(this, 'banner-preview', 'banner-drop-area')">
                            </div>
                            @error('banner') <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="primary_color" class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Brand Color</label>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <input type="color" id="primary_color_picker" style="width: 40px; height: 40px; padding: 0; border: none; border-radius: 4px; cursor: pointer;" value="{{ old('primary_color', '#6366f1') }}" onchange="document.getElementById('primary_color').value = this.value">
                            <input type="text" name="primary_color" id="primary_color" class="form-input" style="width: 120px;" value="{{ old('primary_color', '#6366f1') }}" onkeyup="if(this.value.match(/^#[0-9a-fA-F]{6}$/)) document.getElementById('primary_color_picker').value = this.value">
                        </div>
                        @error('primary_color') <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Team Settings --}}
                <div class="card" style="padding: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Team Settings</h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <x-input type="number" name="min_team_size" label="Min Team Size" :value="old('min_team_size', 1)" min="1" required />
                        <x-input type="number" name="max_team_size" label="Max Team Size" :value="old('max_team_size', 4)" min="1" required />
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 14px; font-weight: 500;">Allow solo participants</div>
                            <div style="font-size: 13px; color: var(--text-muted);">Users can register without a team</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="allow_solo" value="1" {{ old('allow_solo') ? 'checked' : '' }}>
                            <div class="toggle-track">
                                <div class="toggle-thumb"></div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="card" style="padding: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Timeline</h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <x-input type="datetime-local" name="registration_opens_at" label="Registration Opens" :value="old('registration_opens_at')" required />
                        <x-input type="datetime-local" name="registration_closes_at" label="Registration Closes" :value="old('registration_closes_at')" required />
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <x-input type="datetime-local" name="submission_opens_at" label="Submission Opens" :value="old('submission_opens_at')" required />
                        <x-input type="datetime-local" name="submission_closes_at" label="Submission Closes" :value="old('submission_closes_at')" required />
                    </div>

                    <div>
                        <x-input type="datetime-local" name="results_at" label="Results Announcement (optional)" :value="old('results_at')" />
                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                {{-- Publishing --}}
                <div class="card" style="padding: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Publishing</h2>
                    
                    <div style="margin-bottom: 24px;">
                        <span style="font-size: 13px; color: var(--text-muted); margin-right: 8px;">Status:</span>
                        <x-badge variant="secondary">Draft</x-badge>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                        <button type="submit" name="save_draft" value="1" class="btn btn-secondary" style="width: 100%; justify-content: center;">Save as Draft</button>
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Create Hackathon</button>
                    </div>
                    
                    <div style="font-size: 12px; color: var(--text-muted); text-align: center;">
                        You can publish after reviewing all details
                    </div>
                </div>

                {{-- Segments --}}
                <div class="card" style="padding: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Segments</h2>
                    <p style="font-size: 13px; color: var(--text-muted);">Save the hackathon first to add segments</p>
                </div>

            </div>
        </div>
    </form>

    <script>
        function updateSlugPreview(title) {
            const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            document.getElementById('slug-preview').innerText = slug ? slug + '-xxxxxx' : 'auto-generated-slug';
        }

        function previewImage(input, previewId, dropAreaId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).style.display = 'block';
                    document.getElementById(dropAreaId).style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
