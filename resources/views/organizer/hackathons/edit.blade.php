@extends('layouts.app')

@section('title', 'Edit Hackathon')

@section('content')
    <div style="margin-bottom: 24px;">
        <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
            <a href="{{ route('organizer.hackathons.index') }}" style="color: inherit; text-decoration: none;">My Hackathons</a> / Edit Hackathon
        </div>
        <h1 class="text-page-title">Edit Hackathon</h1>
    </div>

    @if (session('success'))
        <div style="margin-bottom: 24px;">
            <x-alert type="success" :message="session('success')" />
        </div>
    @endif
    @if ($errors->any())
        <div style="margin-bottom: 24px;">
            <x-alert type="error" message="Please check the form below for errors." />
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 24px;">
        
        {{-- Left Column --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <form id="hackathon-form" method="POST" action="{{ route('organizer.hackathons.update', $hackathon) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                {{-- Basic Information --}}
                <div class="card" style="padding: 24px; margin-bottom: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Basic Information</h2>
                    
                    <div style="margin-bottom: 16px;">
                        <x-input type="text" name="title" id="title" label="Hackathon Title" :value="old('title', $hackathon->title)" required />
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                            URL: /h/<span id="slug-preview" style="color: var(--text-primary);">{{ $hackathon->slug }}</span>
                            <span style="margin-left: 8px; font-style: italic;">(Slug cannot be changed)</span>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <x-input type="text" name="tagline" label="Tagline (optional)" :value="old('tagline', $hackathon->tagline)" />
                    </div>

                    <div>
                        <label for="description" class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Description</label>
                        <textarea name="description" id="description" class="form-input" style="width: 100%; min-height: 160px; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius-md);" required>{{ old('description', $hackathon->description) }}</textarea>
                        @error('description') <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Branding --}}
                <div class="card" style="padding: 24px; margin-bottom: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Branding</h2>
                    
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <label class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Logo</label>
                            <div style="position: relative; width: 120px; height: 120px;">
                                <img id="logo-preview" src="{{ $hackathon->logo ? Storage::url($hackathon->logo) : '' }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg); display: {{ $hackathon->logo ? 'block' : 'none' }}; cursor: pointer; z-index: 2;" onclick="document.getElementById('logo-input').click()">
                                <div id="logo-drop-area" style="width: 100%; height: 100%; border: 2px dashed var(--border); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; background: var(--surface-alt); cursor: pointer;" onclick="document.getElementById('logo-input').click()">
                                    <span style="font-size: 13px; color: var(--text-muted);">Replace Logo</span>
                                </div>
                                <input type="file" name="logo" id="logo-input" accept="image/*" style="display: none;" onchange="previewImage(this, 'logo-preview', 'logo-drop-area')">
                            </div>
                            @error('logo') <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Banner</label>
                            <div style="position: relative; width: 100%; height: 140px;">
                                <img id="banner-preview" src="{{ $hackathon->banner ? Storage::url($hackathon->banner) : '' }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg); display: {{ $hackathon->banner ? 'block' : 'none' }}; cursor: pointer; z-index: 2;" onclick="document.getElementById('banner-input').click()">
                                <div id="banner-drop-area" style="width: 100%; height: 100%; border: 2px dashed var(--border); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; background: var(--surface-alt); cursor: pointer;" onclick="document.getElementById('banner-input').click()">
                                    <span style="font-size: 13px; color: var(--text-muted);">Replace Banner</span>
                                </div>
                                <input type="file" name="banner" id="banner-input" accept="image/*" style="display: none;" onchange="previewImage(this, 'banner-preview', 'banner-drop-area')">
                            </div>
                            @error('banner') <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="primary_color" class="form-label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Brand Color</label>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <input type="color" id="primary_color_picker" style="width: 40px; height: 40px; padding: 0; border: none; border-radius: 4px; cursor: pointer;" value="{{ old('primary_color', $hackathon->primary_color ?? '#6366f1') }}" onchange="document.getElementById('primary_color').value = this.value">
                            <input type="text" name="primary_color" id="primary_color" class="form-input" style="width: 120px;" value="{{ old('primary_color', $hackathon->primary_color ?? '#6366f1') }}" onkeyup="if(this.value.match(/^#[0-9a-fA-F]{6}$/)) document.getElementById('primary_color_picker').value = this.value">
                        </div>
                        @error('primary_color') <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Team Settings --}}
                <div class="card" style="padding: 24px; margin-bottom: 24px;">
                    <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Team Settings</h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <x-input type="number" name="min_team_size" label="Min Team Size" :value="old('min_team_size', $hackathon->min_team_size)" min="1" required />
                        <x-input type="number" name="max_team_size" label="Max Team Size" :value="old('max_team_size', $hackathon->max_team_size)" min="1" required />
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 14px; font-weight: 500;">Allow solo participants</div>
                            <div style="font-size: 13px; color: var(--text-muted);">Users can register without a team</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="allow_solo" value="1" {{ old('allow_solo', $hackathon->allow_solo) ? 'checked' : '' }}>
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
                        <x-input type="datetime-local" name="registration_opens_at" label="Registration Opens" :value="old('registration_opens_at', $hackathon->registration_opens_at?->format('Y-m-d\TH:i'))" required />
                        <x-input type="datetime-local" name="registration_closes_at" label="Registration Closes" :value="old('registration_closes_at', $hackathon->registration_closes_at?->format('Y-m-d\TH:i'))" required />
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <x-input type="datetime-local" name="submission_opens_at" label="Submission Opens" :value="old('submission_opens_at', $hackathon->submission_opens_at?->format('Y-m-d\TH:i'))" required />
                        <x-input type="datetime-local" name="submission_closes_at" label="Submission Closes" :value="old('submission_closes_at', $hackathon->submission_closes_at?->format('Y-m-d\TH:i'))" required />
                    </div>

                    <div>
                        <x-input type="datetime-local" name="results_at" label="Results Announcement (optional)" :value="old('results_at', $hackathon->results_at?->format('Y-m-d\TH:i'))" />
                    </div>
                </div>
            </form>
        </div>

        {{-- Right Column --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            {{-- Publishing --}}
            <div class="card" style="padding: 24px;">
                <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Publishing</h2>
                
                <div style="margin-bottom: 24px;">
                    <span style="font-size: 13px; color: var(--text-muted); margin-right: 8px;">Status:</span>
                    @php
                        $variant = match($hackathon->status->value) {
                            'published' => 'primary',
                            'ongoing' => 'success',
                            default => 'secondary'
                        };
                    @endphp
                    <x-badge :variant="$variant">{{ ucfirst($hackathon->status->value) }}</x-badge>
                </div>

                <div style="margin-bottom: 16px;">
                    <button type="submit" form="hackathon-form" class="btn btn-primary" style="width: 100%; justify-content: center;">Save Changes</button>
                </div>
            </div>

            {{-- Segments Manager --}}
            <div class="card" style="padding: 24px;">
                <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Segments</h2>
                
                <div id="segments-container" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                    @foreach ($hackathon->segments as $segment)
                        <div class="segment-row" data-id="{{ $segment->id }}" style="background: var(--surface-alt); padding: 12px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                                <input type="text" class="form-input segment-name" value="{{ $segment->name }}" placeholder="Segment Name" style="flex: 1; padding: 4px 8px; height: 32px;">
                                <button type="button" class="btn btn-primary btn-sm" onclick="saveSegment({{ $segment->id }}, this)">Save</button>
                                <button type="button" class="btn btn-secondary btn-sm" style="color: var(--danger); padding: 4px;" onclick="deleteSegment({{ $segment->id }}, this)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path></svg>
                                </button>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="font-size: 12px; color: var(--text-muted);">
                                    @if ($segment->rulebook)
                                        <a href="{{ Storage::url($segment->rulebook) }}" target="_blank" style="color: var(--accent); text-decoration: none;">View Rulebook</a>
                                    @else
                                        No rulebook
                                    @endif
                                </div>
                                <input type="file" accept="application/pdf" class="segment-rulebook" style="font-size: 12px; width: 180px;">
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;" onclick="addSegmentRow()">+ Add Segment</button>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, previewId, dropAreaId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Segments JS Manager
        const hackathonId = {{ $hackathon->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function addSegmentRow() {
            const container = document.getElementById('segments-container');
            const row = document.createElement('div');
            row.className = 'segment-row';
            row.style.cssText = 'background: var(--surface-alt); padding: 12px; border-radius: var(--radius-md); border: 1px solid var(--border);';
            row.innerHTML = `
                <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                    <input type="text" class="form-input segment-name" placeholder="Segment Name" style="flex: 1; padding: 4px 8px; height: 32px;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveSegment('new', this)">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm" style="color: var(--danger); padding: 4px;" onclick="this.closest('.segment-row').remove()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path></svg>
                    </button>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="font-size: 12px; color: var(--text-muted);">No rulebook</div>
                    <input type="file" accept="application/pdf" class="segment-rulebook" style="font-size: 12px; width: 180px;">
                </div>
            `;
            container.appendChild(row);
        }

        async function saveSegment(id, btn) {
            const row = btn.closest('.segment-row');
            const name = row.querySelector('.segment-name').value;
            const fileInput = row.querySelector('.segment-rulebook');
            
            if (!name) return alert('Segment name is required');
            
            btn.textContent = 'Saving...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('name', name);
            if (fileInput.files[0]) {
                formData.append('rulebook', fileInput.files[0]);
            }

            let url = `/organizer/hackathons/${hackathonId}/segments`;
            if (id !== 'new') {
                url += `/${id}`;
                formData.append('_method', 'PUT');
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });
                
                if (res.ok) {
                    window.location.reload();
                } else {
                    const data = await res.json();
                    alert(data.message || 'Error saving segment');
                    btn.textContent = 'Save';
                    btn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred');
                btn.textContent = 'Save';
                btn.disabled = false;
            }
        }

        async function deleteSegment(id, btn) {
            if (!confirm('Delete this segment?')) return;
            
            btn.disabled = true;
            try {
                const res = await fetch(`/organizer/hackathons/${hackathonId}/segments/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                
                if (res.ok) {
                    btn.closest('.segment-row').remove();
                } else {
                    alert('Error deleting segment');
                    btn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
            }
        }
    </script>
@endsection
