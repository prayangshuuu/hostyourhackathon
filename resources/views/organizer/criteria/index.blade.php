@extends('layouts.app')

@section('title', 'Scoring Criteria')

@section('content')
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('organizer.hackathons.index') }}">My Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ $hackathon->title }}</a>
            <span class="separator">/</span>
            <span>Criteria</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Scoring Criteria</h1>
        </div>
    </div>

    <x-alert />

    <x-card title="Criteria">
        <div id="criteria-list">
            @forelse($criteria as $criterion)
                <div class="criterion-row" style="padding: 12px 0; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; gap: 12px;" data-id="{{ $criterion->id }}">
                    <input type="text" class="form-input criteria-name flex-1" value="{{ $criterion->name }}" placeholder="Criterion Name" style="flex: 1;">
                    <span style="font-size: 13px; color: var(--text-secondary);">/ Max:</span>
                    <input type="number" class="form-input criteria-max" value="{{ $criterion->max_score }}" min="1" style="width: 72px;">
                    <button class="btn btn-danger btn-ghost delete-criterion" style="padding: 8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                    <span class="save-indicator" style="color: var(--success); font-size: 12px; font-weight: 500; opacity: 0; transition: opacity 0.2s; width: 40px;">Saved</span>
                </div>
            @empty
                <div id="empty-state" style="text-align: center; padding: 24px; color: var(--text-muted);">
                    No criteria yet. Add your first criterion above.
                </div>
            @endforelse
        </div>

        <div style="margin-top: 16px;">
            <button id="add-criterion-btn" class="btn btn-ghost" style="display: flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Criterion
            </button>
        </div>
    </x-card>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('criteria-list');
            const addBtn = document.getElementById('add-criterion-btn');
            const emptyState = document.getElementById('empty-state');
            const hackathonId = '{{ $hackathon->id }}';

            function attachEvents(row) {
                const nameInput = row.querySelector('.criteria-name');
                const maxInput = row.querySelector('.criteria-max');
                const deleteBtn = row.querySelector('.delete-criterion');
                const saveIndicator = row.querySelector('.save-indicator');

                const save = () => {
                    const id = row.dataset.id;
                    const name = nameInput.value.trim();
                    const maxScore = parseInt(maxInput.value, 10);

                    if (!name || isNaN(maxScore) || maxScore < 1) return;

                    let url = `/organizer/hackathons/${hackathonId}/criteria`;
                    let method = 'POST';
                    if (id) {
                        url += `/${id}`;
                        method = 'PATCH';
                    }

                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            max_score: maxScore
                        })
                    }).then(res => res.json()).then(data => {
                        if (method === 'POST' && data.id) {
                            row.dataset.id = data.id;
                        }
                        saveIndicator.style.opacity = '1';
                        setTimeout(() => {
                            saveIndicator.style.opacity = '0';
                        }, 1500);
                    }).catch(err => console.error(err));
                };

                nameInput.addEventListener('blur', save);
                maxInput.addEventListener('blur', save);

                deleteBtn.addEventListener('click', () => {
                    if (confirm('Are you sure you want to delete this criterion?')) {
                        const id = row.dataset.id;
                        if (id) {
                            fetch(`/organizer/hackathons/${hackathonId}/criteria/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            }).then(() => {
                                row.remove();
                                if(list.children.length === 0 && emptyState) {
                                    emptyState.style.display = 'block';
                                }
                            });
                        } else {
                            row.remove();
                            if(list.children.length === 0 && emptyState) {
                                emptyState.style.display = 'block';
                            }
                        }
                    }
                });
            }

            // Attach events to existing rows
            list.querySelectorAll('.criterion-row').forEach(attachEvents);

            // Add new row
            addBtn.addEventListener('click', () => {
                if(emptyState) emptyState.style.display = 'none';

                const div = document.createElement('div');
                div.className = 'criterion-row';
                div.style.cssText = 'padding: 12px 0; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; gap: 12px;';
                div.innerHTML = `
                    <input type="text" class="form-input criteria-name flex-1" placeholder="Criterion Name" style="flex: 1;">
                    <span style="font-size: 13px; color: var(--text-secondary);">/ Max:</span>
                    <input type="number" class="form-input criteria-max" value="10" min="1" style="width: 72px;">
                    <button class="btn btn-danger btn-ghost delete-criterion" style="padding: 8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                    <span class="save-indicator" style="color: var(--success); font-size: 12px; font-weight: 500; opacity: 0; transition: opacity 0.2s; width: 40px;">Saved</span>
                `;
                list.appendChild(div);
                attachEvents(div);
                div.querySelector('.criteria-name').focus();
            });
        });
    </script>
    @endpush
@endsection
