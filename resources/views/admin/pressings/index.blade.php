@extends('layouts.app', ['title' => 'Gestion des Pressings'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-[#374151]/20 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#000]">GESTION DES PRESSINGS</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-1">
                Liste et administration des agences du réseau
            </p>
        </div>
        <div>
            <a href="{{ route('admin.pressings.create') }}" class="px-4 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition">
                + Ajouter une agence
            </a>
        </div>
    </div>

    <!-- Search form -->
    <div class="my-6">
        <form method="GET" action="{{ route('admin.pressings.index') }}" class="flex max-w-md gap-2">
            <input
                type="text"
                name="q"
                value="{{ $search }}"
                placeholder="Rechercher par nom, ville, quartier..."
                class="flex-1 px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]"
            >
            <button type="submit" class="px-4 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition">
                Filtrer
            </button>
            @if($search)
                <a href="{{ route('admin.pressings.index') }}" class="px-3 py-2 bg-[#F3F4F6] text-[#374151] border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider hover:bg-[#FFF] transition">
                    Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-[#FFF] border border-[#374151]/20 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F3F4F6] border-b border-[#374151]/20 text-[11px] uppercase tracking-wider text-[#374151]">
                        <th class="py-3 px-6 font-semibold">Nom de l'agence</th>
                        <th class="py-3 px-6 font-semibold">Ville</th>
                        <th class="py-3 px-6 font-semibold">Quartier</th>
                        <th class="py-3 px-6 font-semibold">Téléphone</th>
                        <th class="py-3 px-6 font-semibold text-center">Personnel</th>
                        <th class="py-3 px-6 font-semibold text-center">Prestations</th>
                        <th class="py-3 px-6 font-semibold text-center">Factures</th>
                        <th class="py-3 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]/10 text-xs">
                    @forelse($pressings as $p)
                        <tr class="hover:bg-[#F3F4F6]/50 transition">
                            <td class="py-4 px-6 font-bold text-[#000]">{{ $p->nom }}</td>
                            <td class="py-4 px-6 text-[#374151]">{{ $p->ville }}</td>
                            <td class="py-4 px-6 text-[#374151]">{{ $p->quartier }}</td>
                            <td class="py-4 px-6 font-mono text-[#374151]">{{ $p->telephone ?? '—' }}</td>
                            <td class="py-4 px-6 text-center font-mono">{{ $p->users_count }}</td>
                            <td class="py-4 px-6 text-center font-mono">{{ $p->services_count }}</td>
                            <td class="py-4 px-6 text-center font-mono font-semibold">{{ $p->factures_count }}</td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.pressings.edit', $p) }}" class="inline-block px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider border border-[#374151] text-[#000] hover:bg-[#000] hover:text-[#FFF] transition">
                                    Modifier
                                </a>

                                <form method="POST" action="{{ route('admin.pressings.destroy', $p) }}" class="inline-block" onsubmit="return confirm('Confirmer la suppression de cette agence ?');">
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
                            <td colspan="8" class="py-8 text-center text-xs text-[#374151]">
                                Aucun pressing trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pressings->hasPages())
            <div class="p-4 border-t border-[#374151]/10 bg-[#FFF]">
                {{ $pressings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
