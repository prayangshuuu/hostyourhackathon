@extends('layouts.app')

@section('title', $hackathon->title)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
                <a href="{{ route('organizer.hackathons.index') }}" style="color: inherit; text-decoration: none;">My Hackathons</a> / {{ $hackathon->title }}
            </div>
            <h1 class="text-page-title">{{ $hackathon->title }}</h1>
        </div>
        
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('hackathons.show', $hackathon->slug) }}" class="btn" style="color: var(--accent); background: transparent; border: 1px solid var(--accent);" target="_blank">View Public Page</a>
            <a href="{{ route('organizer.hackathons.edit', $hackathon) }}" class="btn btn-secondary">Edit</a>
            
            <form method="POST" action="{{ route('organizer.hackathons.status', $hackathon) }}" style="margin: 0;">
                @csrf
                @if ($hackathon->status->value === 'draft')
                    <input type="hidden" name="status" value="published">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Publish this hackathon? It will be visible to the public.')">Publish</button>
                @elseif ($hackathon->status->value === 'published')
                    <input type="hidden" name="status" value="ongoing">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Mark as ongoing? This officially starts the hackathon.')">Mark Ongoing</button>
                    <button type="submit" name="status" value="draft" class="btn btn-secondary" onclick="return confirm('Unpublish this hackathon? It will be hidden from the public.')">Unpublish</button>
                @elseif ($hackathon->status->value === 'ongoing')
                    <input type="hidden" name="status" value="ended">
                    <button type="submit" class="btn" style="background: var(--danger); color: white; border: none;" onclick="return confirm('End this hackathon? This will close all submissions.')">End Hackathon</button>
                @elseif ($hackathon->status->value === 'ended')
                    <input type="hidden" name="status" value="archived">
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('Archive this hackathon?')">Archive</button>
                @endif
            </form>
        </div>
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

    {{-- Stat Cards --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px;">
        <div class="card" style="padding: 20px;">
            <div style="font-size: 28px; font-weight: 600; color: var(--text-primary);">{{ $teamsCount }}</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Registered Teams</div>
        </div>
        <div class="card" style="padding: 20px;">
            <div style="font-size: 28px; font-weight: 600; color: var(--text-primary);">{{ $submissionsCount }}</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Submissions</div>
        </div>
        <div class="card" style="padding: 20px;">
            <div style="font-size: 28px; font-weight: 600; color: var(--text-primary);">{{ $judgesCount }}</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Judges Assigned</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 24px;">
        
        {{-- Left Column (Tabs) --}}
        <div>
            {{-- Tab Navigation --}}
            <div style="display: flex; gap: 24px; border-bottom: 1px solid var(--border); margin-bottom: 24px;">
                <button class="tab-btn active" onclick="switchTab('overview', this)" style="padding: 12px 0; background: none; border: none; border-bottom: 2px solid var(--accent); color: var(--accent); font-weight: 500; font-size: 14px; cursor: pointer;">Overview</button>
                <button class="tab-btn" onclick="switchTab('segments', this)" style="padding: 12px 0; background: none; border: none; border-bottom: 2px solid transparent; color: var(--text-secondary); font-weight: 500; font-size: 14px; cursor: pointer;">Segments</button>
                <button class="tab-btn" onclick="switchTab('organizers', this)" style="padding: 12px 0; background: none; border: none; border-bottom: 2px solid transparent; color: var(--text-secondary); font-weight: 500; font-size: 14px; cursor: pointer;">Organizers</button>
            </div>

            {{-- Overview Tab --}}
            <div id="tab-overview" class="tab-pane" style="display: block;">
                <div class="card" style="overflow: hidden; margin-bottom: 24px;">
                    @if ($hackathon->banner)
                        <img src="{{ Storage::url($hackathon->banner) }}" alt="" style="width: 100%; height: 120px; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 120px; background: var(--surface-alt);"></div>
                    @endif
                    
                    <div style="padding: 24px; position: relative;">
                        @if ($hackathon->logo)
                            <img src="{{ Storage::url($hackathon->logo) }}" alt="" style="width: 64px; height: 64px; border-radius: var(--radius-sm); border: 4px solid var(--surface); position: absolute; top: -32px; left: 24px; object-fit: cover;">
                        @else
                            <div style="width: 64px; height: 64px; border-radius: var(--radius-sm); border: 4px solid var(--surface); background: var(--surface-alt); position: absolute; top: -32px; left: 24px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 600; color: var(--text-muted);">
                                {{ strtoupper(substr($hackathon->title, 0, 1)) }}
                            </div>
                        @endif
                        
                        <div style="margin-top: 32px;">
                            <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">{{ $hackathon->title }}</h2>
                            <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 16px;">{{ $hackathon->tagline }}</p>
                            
                            <div id="desc-container" style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                                <div id="desc-truncated">{{ Str::limit($hackathon->description, 200) }}</div>
                                <div id="desc-full" style="display: none;">{{ $hackathon->description }}</div>
                                @if (strlen($hackathon->description) > 200)
                                    <button onclick="toggleDesc()" id="desc-btn" style="background: none; border: none; color: var(--accent); padding: 0; margin-top: 8px; cursor: pointer; font-size: 13px; font-weight: 500;">Show more</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" style="padding: 24px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Timeline</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @php
                            $timeline = [
                                ['label' => 'Registration Opens', 'date' => $hackathon->registration_opens_at],
                                ['label' => 'Registration Closes', 'date' => $hackathon->registration_closes_at],
                                ['label' => 'Submission Opens', 'date' => $hackathon->submission_opens_at],
                                ['label' => 'Submission Closes', 'date' => $hackathon->submission_closes_at],
                                ['label' => 'Results', 'date' => $hackathon->results_at],
                            ];
                        @endphp
                        
                        @foreach ($timeline as $t)
                            @if ($t['date'])
                                @php $past = $t['date']->isPast(); @endphp
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="color: {{ $past ? 'var(--success)' : 'var(--text-muted)' }}; width: 20px; text-align: center;">
                                        @if ($past)
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        @else
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        @endif
                                    </div>
                                    <div style="flex: 1; font-size: 14px; color: var(--text-primary); font-weight: 500;">{{ $t['label'] }}</div>
                                    <div style="font-size: 14px; color: var(--text-secondary);">{{ $t['date']->format('M j, Y, g:i a') }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Segments Tab --}}
            <div id="tab-segments" class="tab-pane" style="display: none;">
                <div class="card" style="padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="font-size: 16px; font-weight: 600;">Segments</h3>
                    </div>
                    
                    <div id="segments-container" style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                        @foreach ($hackathon->segments as $segment)
                            <div class="segment-row" data-id="{{ $segment->id }}" style="background: var(--surface-alt); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                                    <input type="text" class="form-input segment-name" value="{{ $segment->name }}" placeholder="Segment Name" style="flex: 1;">
                                    <button type="button" class="btn btn-primary" onclick="saveSegment({{ $segment->id }}, this)">Save</button>
                                    <button type="button" class="btn btn-secondary" style="color: var(--danger);" onclick="deleteSegment({{ $segment->id }}, this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path></svg>
                                    </button>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="font-size: 13px; color: var(--text-muted);">
                                        @if ($segment->rulebook)
                                            <a href="{{ Storage::url($segment->rulebook) }}" target="_blank" style="color: var(--accent); text-decoration: none; font-weight: 500;">View Rulebook</a>
                                        @else
                                            No rulebook uploaded
                                        @endif
                                    </div>
                                    <input type="file" accept="application/pdf" class="segment-rulebook form-input" style="padding: 4px; font-size: 12px; width: 220px;">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-secondary" style="width: 100%; justify-content: center;" onclick="addSegmentRow()">+ Add Segment</button>
                </div>
            </div>

            {{-- Organizers Tab --}}
            <div id="tab-organizers" class="tab-pane" style="display: none;">
                <div class="card" style="padding: 24px; margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Add Organizer</h3>
                    <form method="POST" action="{{ route('organizer.hackathons.organizers.store', $hackathon) }}" style="display: flex; gap: 12px;">
                        @csrf
                        <input type="email" name="email" class="form-input" placeholder="Organizer's email address" required style="flex: 1;">
                        <button type="submit" class="btn btn-secondary">Add Organizer</button>
                    </form>
                </div>
                
                <div class="card" style="padding: 24px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Current Organizers</h3>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        {{-- Creator --}}
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">
                                    {{ strtoupper(substr($hackathon->creator->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 500; color: var(--text-primary);">{{ $hackathon->creator->name }} <x-badge variant="primary">Owner</x-badge></div>
                                    <div style="font-size: 13px; color: var(--text-muted);">{{ $hackathon->creator->email }}</div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Co-organizers --}}
                        @foreach ($hackathon->organizers as $org)
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--surface-alt); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">
                                        {{ strtoupper(substr($org->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 500; color: var(--text-primary);">{{ $org->name }}</div>
                                        <div style="font-size: 13px; color: var(--text-muted);">{{ $org->email }}</div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('organizer.hackathons.organizers.destroy', [$hackathon, $org]) }}" onsubmit="return confirm('Remove this organizer?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn" style="background: transparent; color: var(--danger); font-size: 13px; font-weight: 500; border: none;">Remove</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
        </div>

        {{-- Right Column --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            {{-- Quick Stats --}}
            <div class="card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Quick Info</h3>
                
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: var(--text-secondary);">Registration</span>
                        @if ($hackathon->registration_opens_at && $hackathon->registration_closes_at && now()->between($hackathon->registration_opens_at, $hackathon->registration_closes_at))
                            <x-badge variant="success">Open</x-badge>
                        @else
                            <x-badge variant="secondary">Closed</x-badge>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: var(--text-secondary);">Submission</span>
                        @if ($hackathon->submission_opens_at && $hackathon->submission_closes_at && now()->between($hackathon->submission_opens_at, $hackathon->submission_closes_at))
                            <x-badge variant="success">Open</x-badge>
                        @else
                            <x-badge variant="secondary">Closed</x-badge>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: var(--text-secondary);">Results</span>
                        <span style="font-size: 14px; color: var(--text-primary); font-weight: 500;">
                            {{ $hackathon->results_at ? $hackathon->results_at->format('M j, Y') : 'TBA' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card" style="padding: 24px; border: 1px solid rgba(239, 68, 68, 0.2);">
                <h3 style="font-size: 16px; font-weight: 600; color: var(--danger); margin-bottom: 12px;">Danger Zone</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Once you delete a hackathon, there is no going back. Please be certain.</p>
                <form method="POST" action="{{ route('organizer.hackathons.destroy', $hackathon) }}" onsubmit="return confirm('PERMANENTLY delete this hackathon?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background: var(--danger); color: white; width: 100%; justify-content: center; border: none;">Delete Hackathon</button>
                </form>
            </div>

        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        function toggleDesc() {
            const trunc = document.getElementById('desc-truncated');
            const full = document.getElementById('desc-full');
            const btn = document.getElementById('desc-btn');
            
            if (full.style.display === 'none') {
                trunc.style.display = 'none';
                full.style.display = 'block';
                btn.innerText = 'Show less';
            } else {
                trunc.style.display = 'block';
                full.style.display = 'none';
                btn.innerText = 'Show more';
            }
        }

        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.style.color = 'var(--text-secondary)';
                b.style.borderBottomColor = 'transparent';
                b.classList.remove('active');
            });
            
            document.getElementById('tab-' + tabId).style.display = 'block';
            btn.style.color = 'var(--accent)';
            btn.style.borderBottomColor = 'var(--accent)';
            btn.classList.add('active');
        }

        // Segments JS Manager
        const hackathonId = {{ $hackathon->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function addSegmentRow() {
            const container = document.getElementById('segments-container');
            const row = document.createElement('div');
            row.className = 'segment-row';
            row.style.cssText = 'background: var(--surface-alt); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border);';
            row.innerHTML = `
                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                    <input type="text" class="form-input segment-name" placeholder="Segment Name" style="flex: 1;">
                    <button type="button" class="btn btn-primary" onclick="saveSegment('new', this)">Save</button>
                    <button type="button" class="btn btn-secondary" style="color: var(--danger);" onclick="this.closest('.segment-row').remove()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path></svg>
                    </button>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="font-size: 13px; color: var(--text-muted);">No rulebook</div>
                    <input type="file" accept="application/pdf" class="segment-rulebook form-input" style="padding: 4px; font-size: 12px; width: 220px;">
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
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
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
