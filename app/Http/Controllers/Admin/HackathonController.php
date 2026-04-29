<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HackathonController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'active');

        $active = Hackathon::withCount(['teams', 'submissions', 'segments'])
            ->with('creator')
            ->latest()
            ->get();

        $archived = Hackathon::onlyTrashed()
            ->withCount(['teams', 'submissions', 'segments'])
            ->with('creator')
            ->latest()
            ->get();

        return view('admin.hackathons.index', compact('active', 'archived', 'tab'));
    }

    public function show(Hackathon $hackathon): View
    {
        $hackathon->load(['segments', 'teams', 'submissions', 'creator', 'organizers']);

        return view('admin.hackathons.show', compact('hackathon'));
    }

    public function forceDelete(string $id): RedirectResponse
    {
        $hackathon = Hackathon::withTrashed()->findOrFail($id);
        $title = $hackathon->title;
        $hackathon->forceDelete();

        return redirect()
            ->route('admin.hackathons.index', ['tab' => 'archived'])
            ->with('success', "Hackathon \"{$title}\" permanently deleted.");
    }

    public function restore(string $id): RedirectResponse
    {
        $hackathon = Hackathon::withTrashed()->findOrFail($id);
        $hackathon->restore();

        return redirect()
            ->route('admin.hackathons.index')
            ->with('success', "Hackathon \"{$hackathon->title}\" restored.");
    }

    public function destroy(Hackathon $hackathon): RedirectResponse
    {
        $title = $hackathon->title;
        $hackathon->delete();

        return redirect()
            ->route('admin.hackathons.index')
            ->with('success', "Hackathon \"{$title}\" moved to archived.");
    }
}
