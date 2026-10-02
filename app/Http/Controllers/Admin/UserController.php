<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pressing;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the staff members.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $role = $request->query('role');
        $pressingId = $request->query('pressing_id');

        $users = User::query()
            ->with('pressing')
            ->withCount('factures')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($pressingId, function ($query, $pressingId) {
                $query->where('pressing_id', $pressingId);
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $pressings = Pressing::orderBy('nom')->get();

        return view('admin.users.index', [
            'users' => $users,
            'pressings' => $pressings,
            'search' => $search,
            'selectedRole' => $role,
            'selectedPressingId' => $pressingId,
        ]);
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create(): View
    {
        $pressings = Pressing::orderBy('nom')->get();

        return view('admin.users.create', [
            'pressings' => $pressings,
        ]);
    }

    /**
     * Store a newly created staff member in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'caissier'])],
            'pressing_id' => [
                Rule::requiredIf(fn () => $request->input('role') === 'caissier'),
                'nullable',
                'exists:pressings,id',
            ],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('status', 'Compte utilisateur créé avec succès.');
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(User $user): View
    {
        $pressings = Pressing::orderBy('nom')->get();

        return view('admin.users.edit', [
            'user' => $user,
            'pressings' => $pressings,
        ]);
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'caissier'])],
            'pressing_id' => [
                Rule::requiredIf(fn () => $request->input('role') === 'caissier'),
                'nullable',
                'exists:pressings,id',
            ],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('status', 'Compte utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors([
                'error' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ]);
        }

        if ($user->factures()->exists()) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer cet utilisateur car des factures lui sont rattachées.',
            ]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'Compte utilisateur supprimé avec succès.');
    }
}
