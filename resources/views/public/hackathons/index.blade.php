@extends('layouts.public')

@section('title', 'Hackathons — ' . config('app.name'))
@section('meta_description', 'Browse and filter hackathons on ' . config('app.name'))

@section('content')
    <div class="page-header" style="margin-bottom:32px;">
        <h1 class="text-page-title">Hackathons</h1>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar" id="filter-bar">
        <div class="pill-toggle-group" id="pill-toggles">
            <button class="pill-toggle active" data-filter="all">All</button>
            <button class="pill-toggle" data-filter="published">Upcoming</button>
            <button class="pill-toggle" data-filter="ongoing">Ongoing</button>
            <button class="pill-toggle" data-filter="ended">Ended</button>
        </div>
        <div class="filter-search">
            <input type="text" class="form-input" id="search-input" placeholder="Search hackathons…" style="width:240px;">
        </div>
    </div>

    {{-- Card Grid --}}
    <div class="grid-3" id="hackathon-grid">
        @foreach ($hackathons as $hackathon)
            <div class="hackathon-grid-item" data-status="{{ $hackathon->status->value }}" data-title="{{ strtolower($hackathon->title) }}">
                @include('components.hackathon-card', ['hackathon' => $hackathon])
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pills = document.querySelectorAll('.pill-toggle');
            const items = document.querySelectorAll('.hackathon-grid-item');
            const searchInput = document.getElementById('search-input');

            function filterItems() {
                const activeFilter = document.querySelector('.pill-toggle.active').dataset.filter;
                const searchTerm = searchInput.value.toLowerCase();

                items.forEach(function (item) {
                    const matchesFilter = activeFilter === 'all' || item.dataset.status === activeFilter;
                    const matchesSearch = !searchTerm || item.dataset.title.includes(searchTerm);
                    item.style.display = (matchesFilter && matchesSearch) ? '' : 'none';
                });
            }

            pills.forEach(function (pill) {
                pill.addEventListener('click', function () {
                    pills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    filterItems();
                });
            });

            searchInput.addEventListener('input', filterItems);
        });
    </script>
    @endpush
@endsection
