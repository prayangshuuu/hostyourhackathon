<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withTrashed()->with('roles');

        if ($role = $request->get('role')) {
            $query->role($role);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'ilike', "%{$search}%")
                  ->orWhere('name', 'ilike', "%{$search}%");
            });
        }
        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $users = $query->latest()->paginate(20);
        $roles = RoleEnum::values();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = RoleEnum::values();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate(['role' => 'required|string|in:' . implode(',', RoleEnum::values())]);

        $user->syncRoles([$request->role]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Role updated for {$user->name}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} deleted.");
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} restored.");
    }

    /**
     * Start impersonating a user.
     */
    public function impersonate(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        session()->put('impersonating_from', auth()->id());
        session()->put('impersonating_name', $user->name);
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', "Now impersonating {$user->name}.");
    }

    /**
     * Stop impersonating — return to original admin.
     */
    public function stopImpersonation(): RedirectResponse
    {
        $adminId = session()->pull('impersonating_from');
        session()->forget('impersonating_name');

        if ($adminId) {
            auth()->loginUsingId($adminId);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Impersonation ended.');
    }
}
