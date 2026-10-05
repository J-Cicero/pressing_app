@extends('layouts.app', ['title' => 'Gestion du Personnel'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Gestion du Personnel</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-full">
                    {{ $users->total() }} utilisateur(s)
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Administration des accès, rôles et affectation des agences
            </p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouveau compte</span>
            </a>
        </div>
    </div>

    <!-- Filters form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Nom ou email..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                >
            </div>
            <div>
                <select name="role" class="w-full px-3 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition">
                    <option value="">Tous les rôles</option>
                    <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="caissier" {{ $selectedRole === 'caissier' ? 'selected' : '' }}>Caissier</option>
                </select>
            </div>
            <div>
                <select name="pressing_id" class="w-full px-3 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition">
                    <option value="">Toutes les agences</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition flex-1 shadow-xs">
                    Filtrer
                </button>
                @if($search || $selectedRole || $selectedPressingId)
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="py-3.5 px-6">Nom complet</th>
                        <th class="py-3.5 px-6">Email</th>
                        <th class="py-3.5 px-6">Rôle</th>
                        <th class="py-3.5 px-6">Agence Affectée</th>
                        <th class="py-3.5 px-6 text-center">Factures créées</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $u->name }}
                                @if($u->id === auth()->id())
                                    <span class="ml-1 text-[10px] font-semibold px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-700">Vous</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-600">{{ $u->email }}</td>
                            <td class="py-4 px-6">
                                @if($u->role === 'admin')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        Caissier
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                @if($u->pressing)
                                    <span class="font-semibold text-slate-900">{{ $u->pressing->nom }}</span>
                                    <span class="text-slate-400 text-[11px]">({{ $u->pressing->ville }})</span>
                                @else
                                    <span class="text-slate-400 italic">Aucune (Global)</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center font-mono font-bold text-indigo-600">
                                {{ $u->factures_count }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.users.edit', $u) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 transition">
                                    Modifier
                                </a>

                                @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-block" onsubmit="return confirm('Confirmer la suppression de cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 transition cursor-pointer">
                                            Supprimer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-slate-500">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-lg">
                                        👥
                                    </div>
                                    <p class="font-medium text-slate-700">Aucun membre du personnel trouvé</p>
                                    <a href="{{ route('admin.users.create') }}" class="inline-block px-3.5 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
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
            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
