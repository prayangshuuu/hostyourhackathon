@extends('layouts.app')

@section('title', 'Scoring Criteria')

@section('content')
    <x-page-header 
        title="Scoring Criteria" 
        :description="'Define how judges should evaluate submissions for ' . $hackathon->title"
        :breadcrumbs="[
            'My Hackathons' => route('organizer.hackathons.index'), 
            $hackathon->title => route('organizer.hackathons.show', $hackathon),
            'Criteria' => null
        ]"
    />

    <div class="max-w-3xl space-y-6">
        <x-card title="Criteria List" icon="clipboard-document-list">
            <div class="space-y-4">
                @foreach ($hackathon->criteria as $criterion)
                    <div class="flex items-center gap-3 p-4 bg-slate-50 border border-slate-100 rounded-xl">
                        <form method="POST" action="{{ route('organizer.hackathons.criteria.update', [$hackathon, $criterion]) }}" class="flex-1 flex items-center gap-3">
                            @csrf @method('PUT')
                            <div class="flex-1">
                                <x-input name="name" :value="$criterion->name" placeholder="Criterion name" required />
                            </div>
                            <div class="w-24">
                                <x-input type="number" name="max_score" :value="$criterion->max_score" min="1" max="100" placeholder="Max" required />
                            </div>
                            <div class="mb-5">
                                <x-button type="submit" variant="secondary" size="sm">Save</x-button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('organizer.hackathons.criteria.destroy', [$hackathon, $criterion]) }}" onsubmit="return confirm('Delete this criterion?')" class="mb-5">
                            @csrf @method('DELETE')
                            <x-button type="submit" variant="ghost" size="sm" class="text-red-600"><x-heroicon-o-trash class="w-4 h-4" /></x-button>
                        </form>
                    </div>
                @endforeach

                @if ($hackathon->criteria->isEmpty())
                    <x-empty-state icon="clipboard-document" title="No criteria defined" description="Add your first criterion below to start evaluating projects." />
                @endif
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-4">Add New Criterion</h4>
                <form method="POST" action="{{ route('organizer.hackathons.criteria.store', $hackathon) }}" class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <x-input label="Name" name="name" :value="old('name')" placeholder="e.g. Innovation" required />
                    </div>
                    <div class="w-24">
                        <x-input label="Max Score" type="number" name="max_score" :value="old('max_score', 10)" min="1" max="100" required />
                    </div>
                    <div class="mb-5">
                        <x-button type="submit" variant="primary" icon="plus">Add</x-button>
                    </div>
                </form>
            </div>
        </x-card>
    </div>
@endsection
