@extends('layouts.app', ['title' => 'Gestion du Personnel'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900 dark:text-white">Gestion du Personnel</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $users->total() }} utilisateur(s)
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Administration des accès, rôles et affectation des agences
            </p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 text-xs font-bold rounded-xl shadow-md transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4 text-white dark:text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouveau compte</span>
            </a>
        </div>
    </div>

    <!-- Filters form -->
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Nom ou email..."
                    class="w-full pl-9 pr-4 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                >
            </div>
            <div>
                <select name="role" class="w-full px-3 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition">
                    <option value="">Tous les rôles</option>
                    <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="caissier" {{ $selectedRole === 'caissier' ? 'selected' : '' }}>Caissier</option>
                </select>
            </div>
            <div>
                <select name="pressing_id" class="w-full px-3 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition">
                    <option value="">Toutes les agences</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition flex-1 shadow-xs">
                    Filtrer
                </button>
                @if($search || $selectedRole || $selectedPressingId)
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 rounded-xl text-xs font-semibold transition border border-zinc-200 dark:border-zinc-700">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50/80 dark:bg-zinc-950/80 border-b border-zinc-100 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Nom complet</th>
                        <th class="py-3.5 px-6">Email</th>
                        <th class="py-3.5 px-6">Rôle</th>
                        <th class="py-3.5 px-6">Agence Affectée</th>
                        <th class="py-3.5 px-6 text-center">Factures créées</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($users as $u)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                            <td class="py-4 px-6 font-bold text-zinc-900 dark:text-white">
                                {{ $u->name }}
                                @if($u->id === auth()->id())
                                    <span class="ml-1 text-[10px] font-semibold px-2 py-0.5 rounded-md bg-zinc-900 dark:bg-white text-white dark:text-zinc-950">Vous</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono text-zinc-600 dark:text-zinc-400">{{ $u->email }}</td>
                            <td class="py-4 px-6">
                                @if($u->role === 'admin')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 dark:bg-white text-white dark:text-zinc-950">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                        Caissier
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-zinc-600 dark:text-zinc-300">
                                @if($u->pressing)
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $u->pressing->nom }}</span>
                                    <span class="text-zinc-400 dark:text-zinc-500 text-[11px]">({{ $u->pressing->ville }})</span>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500 italic">Aucune (Global)</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center font-mono font-bold text-zinc-900 dark:text-white">
                                {{ $u->factures_count }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.users.edit', $u) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition">
                                    Modifier
                                </a>

                                @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-block" onsubmit="return confirm('Confirmer la suppression de cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-950 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800 transition cursor-pointer">
                                            Supprimer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-zinc-500 dark:text-zinc-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-400 flex items-center justify-center mx-auto text-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-zinc-700 dark:text-zinc-200">Aucun membre du personnel trouvé</p>
                                    <a href="{{ route('admin.users.create') }}" class="inline-block px-3.5 py-1.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-950 rounded-xl text-xs font-bold">
                                        + Nouveau compte
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
