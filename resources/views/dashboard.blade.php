@extends('layouts.app', ['title' => 'Tableau de bord'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-[#000]">Tableau de bord</h1>
        <p class="text-sm text-[#374151]">Session active en tant que <span class="font-semibold text-[#000]">{{ $user->role }}</span>.</p>
    </div>

    <!-- User & Pressing Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#FFF] border border-[#374151]/20 p-6">
            <div class="text-xs uppercase tracking-wider text-[#374151]">Utilisateur connecté</div>
            <div class="mt-2 text-lg font-bold text-[#000]">{{ $user->name }}</div>
            <div class="text-sm text-[#374151]">{{ $user->email }}</div>
            <div class="mt-4 inline-block px-2.5 py-1 text-xs uppercase font-bold tracking-wider {{ $user->isAdmin() ? 'bg-[#000] text-[#FFF]' : 'bg-[#F3F4F6] text-[#000] border border-[#374151]/40' }}">
                Rôle : {{ $user->role }}
            </div>
        </div>

        <div class="bg-[#FFF] border border-[#374151]/20 p-6">
            <div class="text-xs uppercase tracking-wider text-[#374151]">Agence Assignée</div>
            @if($user->pressing)
                <div class="mt-2 text-lg font-bold text-[#000]">{{ $user->pressing->nom }}</div>
                <div class="text-sm text-[#374151]">{{ $user->pressing->quartier }}, {{ $user->pressing->ville }}</div>
                <div class="text-xs text-[#374151] mt-2">Tél: {{ $user->pressing->telephone ?? 'Non renseigné' }}</div>
            @else
                <div class="mt-2 text-sm text-[#374151]">Aucune agence assignée (Administrateur global)</div>
            @endif
        </div>

        <div class="bg-[#FFF] border border-[#374151]/20 p-6">
            <div class="text-xs uppercase tracking-wider text-[#374151]">Règles Fondamentales</div>
            <ul class="mt-2 space-y-1.5 text-xs text-[#374151]">
                <li class="flex items-start">
                    <span class="font-mono mr-1 text-[#000]">•</span>
                    <span>Paiement : 100% au retrait du linge</span>
                </li>
                <li class="flex items-start">
                    <span class="font-mono mr-1 text-[#000]">•</span>
                    <span>Route Key : Numéro de ticket masquant les IDs</span>
                </li>
                <li class="flex items-start">
                    <span class="font-mono mr-1 text-[#000]">•</span>
                    <span>Design : Monochrome strict (#000, #FFF, #F3F4F6, #374151)</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Role-specific sections -->
    @if($user->isAdmin())
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 mb-8">
            <h2 class="text-lg font-bold text-[#000] mb-2">Section Administrateur</h2>
            <p class="text-xs text-[#374151] mb-4">Gestion des agences, utilisateurs et configuration globale des services.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border border-[#374151]/20 p-4 bg-[#F3F4F6]">
                    <div class="text-xs uppercase tracking-wider text-[#374151]">Agences</div>
                    <div class="text-xl font-bold text-[#000] mt-1">{{ \App\Models\Pressing::count() }}</div>
                </div>
                <div class="border border-[#374151]/20 p-4 bg-[#F3F4F6]">
                    <div class="text-xs uppercase tracking-wider text-[#374151]">Utilisateurs</div>
                    <div class="text-xl font-bold text-[#000] mt-1">{{ \App\Models\User::count() }}</div>
                </div>
                <div class="border border-[#374151]/20 p-4 bg-[#F3F4F6]">
                    <div class="text-xs uppercase tracking-wider text-[#374151]">Services</div>
                    <div class="text-xl font-bold text-[#000] mt-1">{{ \App\Models\Service::count() }}</div>
                </div>
                <div class="border border-[#374151]/20 p-4 bg-[#F3F4F6]">
                    <div class="text-xs uppercase tracking-wider text-[#374151]">Factures</div>
                    <div class="text-xl font-bold text-[#000] mt-1">{{ \App\Models\Facture::count() }}</div>
                </div>
            </div>
        </div>
    @endif

    @if($user->isCaissier())
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 mb-8">
            <h2 class="text-lg font-bold text-[#000] mb-2">Section Caisse & Dépôts</h2>
            <p class="text-xs text-[#374151] mb-4">Enregistrement des dépôts, préparation et retrait des vêtements.</p>
            <div class="border-t border-[#374151]/10 pt-4">
                <p class="text-xs text-[#374151]">Espace caisse prêt pour les prochaines étapes de facturation.</p>
            </div>
        </div>
    @endif
</div>
@endsection
