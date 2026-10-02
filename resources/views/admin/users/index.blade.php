@extends('layouts.app', ['title' => 'Gestion du Personnel'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-[#374151]/20 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#000]">GESTION DU PERSONNEL</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-1">
                Comptes utilisateurs, administrateurs et affectation des caissiers
            </p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition">
                + Nouveau compte
            </a>
        </div>
    </div>

    <!-- Filters form -->
    <div class="my-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Recherche par nom ou email..."
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                >
            </div>
            <div>
                <select name="role" class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]">
                    <option value="">Tous les rôles</option>
                    <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="caissier" {{ $selectedRole === 'caissier' ? 'selected' : '' }}>Caissier</option>
                </select>
            </div>
            <div>
                <select name="pressing_id" class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]">
                    <option value="">Toutes les agences</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition flex-1">
                    Filtrer
                </button>
                @if($search || $selectedRole || $selectedPressingId)
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-[#F3F4F6] text-[#374151] border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider hover:bg-[#FFF] transition">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-[#FFF] border border-[#374151]/20 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F3F4F6] border-b border-[#374151]/20 text-[11px] uppercase tracking-wider text-[#374151]">
                        <th class="py-3 px-6 font-semibold">Nom complet</th>
                        <th class="py-3 px-6 font-semibold">Email</th>
                        <th class="py-3 px-6 font-semibold">Rôle</th>
                        <th class="py-3 px-6 font-semibold">Agence Affectée</th>
                        <th class="py-3 px-6 font-semibold text-center">Factures créées</th>
                        <th class="py-3 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]/10 text-xs">
                    @forelse($users as $u)
                        <tr class="hover:bg-[#F3F4F6]/50 transition">
                            <td class="py-4 px-6 font-bold text-[#000]">
                                {{ $u->name }}
                                @if($u->id === auth()->id())
                                    <span class="ml-1 text-[10px] font-mono px-1 py-0.5 bg-[#000] text-[#FFF]">Vous</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono text-[#374151]">{{ $u->email }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $u->role === 'admin' ? 'bg-[#000] text-[#FFF]' : 'bg-[#F3F4F6] text-[#000] border border-[#374151]/40' }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-[#374151]">
                                @if($u->pressing)
                                    <span class="font-medium text-[#000]">{{ $u->pressing->nom }}</span>
                                    <span class="text-[11px] text-[#374151]">({{ $u->pressing->ville }})</span>
                                @else
                                    <span class="text-[#374151] italic">Aucune (Global)</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center font-mono font-semibold">{{ $u->factures_count }}</td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.users.edit', $u) }}" class="inline-block px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider border border-[#374151] text-[#000] hover:bg-[#000] hover:text-[#FFF] transition">
                                    Modifier
                                </a>

                                @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-block" onsubmit="return confirm('Confirmer la suppression de cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider bg-[#F3F4F6] text-[#374151] border border-[#374151]/30 hover:bg-[#000] hover:text-[#FFF] transition">
                                            Supprimer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-[#374151]">
                                Aucun membre du personnel trouvé avec ces critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-[#374151]/10 bg-[#FFF]">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
