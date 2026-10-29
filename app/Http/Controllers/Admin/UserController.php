<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserCreatedMail;
use App\Models\Pressing;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        $plainPassword = $validated['password'];
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        try {
            Mail::to($user->email)->send(new UserCreatedMail($user, $plainPassword));
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'envoi de l\'e-mail de création utilisateur : '.$e->getMessage());
        }

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

        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->withErrors([
                'error' => 'Vous ne pouvez pas rétrograder votre propre rôle administrateur.',
            ]);
        }

        $passwordChanged = ! empty($validated['password']);
        $roleOrAgencyChanged = $user->role !== $validated['role']
            || (int) $user->pressing_id !== (int) ($validated['pressing_id'] ?? 0);

        if ($passwordChanged) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if (($passwordChanged || $roleOrAgencyChanged) && $user->id !== Auth::id()) {
            $user->forceFill(['remember_token' => Str::random(60)])->save();
            $this->revokeUserSessions($user->id);
        }

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

        $this->revokeUserSessions($user->id);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'Compte utilisateur supprimé avec succès.');
    }

    /**
     * Invalidate active database sessions for a given user ID.
     */
    private function revokeUserSessions(int $userId): void
    {
        try {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $userId)
                ->delete();
        } catch (\Throwable) {
            // Session table may not be used when driver is array/file
        }
    }
}
