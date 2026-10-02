@extends('layouts.app', ['title' => 'Catalogue des Prestations'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-[#374151]/20 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#000]">CATALOGUE DES PRESTATIONS</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-1">
                Grille tarifaire des services de pressing par agence
            </p>
        </div>
        <div>
            <a href="{{ route('admin.services.create') }}" class="px-4 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition">
                + Ajouter une prestation
            </a>
        </div>
    </div>

    <!-- Filters form -->
    <div class="my-6">
        <form method="GET" action="{{ route('admin.services.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-2xl">
            <div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Recherche par désignation..."
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                >
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
                @if($search || $selectedPressingId)
                    <a href="{{ route('admin.services.index') }}" class="px-3 py-2 bg-[#F3F4F6] text-[#374151] border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider hover:bg-[#FFF] transition">
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
                        <th class="py-3 px-6 font-semibold">Désignation</th>
                        <th class="py-3 px-6 font-semibold">Agence Associée</th>
                        <th class="py-3 px-6 font-semibold text-right">Prix Unitaire</th>
                        <th class="py-3 px-6 font-semibold text-center">Utilisations en facture</th>
                        <th class="py-3 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]/10 text-xs">
                    @forelse($services as $s)
                        <tr class="hover:bg-[#F3F4F6]/50 transition">
                            <td class="py-4 px-6 font-bold text-[#000]">{{ $s->designation }}</td>
                            <td class="py-4 px-6 text-[#374151]">
                                <span class="font-medium text-[#000]">{{ $s->pressing->nom }}</span>
                                <span class="text-[11px] text-[#374151]">({{ $s->pressing->ville }})</span>
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-[#000]">
                                {{ number_format((float) $s->prix_unitaire, 2, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-center font-mono">{{ $s->ligne_factures_count }}</td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.services.edit', $s) }}" class="inline-block px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider border border-[#374151] text-[#000] hover:bg-[#000] hover:text-[#FFF] transition">
                                    Modifier
                                </a>

                                <form method="POST" action="{{ route('admin.services.destroy', $s) }}" class="inline-block" onsubmit="return confirm('Confirmer la suppression de cette prestation ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider bg-[#F3F4F6] text-[#374151] border border-[#374151]/30 hover:bg-[#000] hover:text-[#FFF] transition">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-[#374151]">
                                Aucune prestation trouvée dans le catalogue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="p-4 border-t border-[#374151]/10 bg-[#FFF]">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
