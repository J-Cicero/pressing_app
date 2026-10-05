@extends('layouts.app', ['title' => 'Tableau de bord'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tableau de bord</h1>
            <p class="text-xs text-slate-500 mt-1">
                Bienvenue <span class="font-semibold text-slate-900">{{ $user->name }}</span> &bull; Session active en tant que <span class="font-semibold text-indigo-600 uppercase">{{ $user->role }}</span>
            </p>
        </div>

        @if($user->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                Accéder au Super Admin &rarr;
            </a>
        @elseif($user->isCaissier())
            <a href="{{ route('caisse.dashboard') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                Accéder au Guichet Caisse &rarr;
            </a>
        @endif
    </div>

    <!-- User & Pressing Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-3">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Utilisateur Connecté</div>
            <div class="text-base font-bold text-slate-900">{{ $user->name }}</div>
            <div class="text-xs text-slate-500 font-mono">{{ $user->email }}</div>
            <div class="inline-flex items-center px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider rounded-lg {{ $user->isAdmin() ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                Rôle : {{ $user->role }}
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-3">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Agence Assignée</div>
            @if($user->pressing)
                <div class="text-base font-bold text-slate-900">{{ $user->pressing->nom }}</div>
                <div class="text-xs text-slate-500">{{ $user->pressing->quartier }}, {{ $user->pressing->ville }}</div>
                <div class="text-xs text-slate-600 font-mono mt-1">📞 Tél: {{ $user->pressing->telephone ?? 'Non renseigné' }}</div>
            @else
                <div class="text-xs text-slate-500 pt-2">Aucune agence assignée (Administrateur global)</div>
            @endif
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-3">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Directives Métier</div>
            <ul class="space-y-2 text-xs text-slate-600">
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 shrink-0"></span>
                    <span>Paiement : 100% au retrait du linge</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 shrink-0"></span>
                    <span>Route Key : N° de ticket masquant les IDs</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 shrink-0"></span>
                    <span>Multi-Agences & Suivi temps réel</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Role-specific Summary Cards -->
    @if($user->isAdmin())
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Vue Synthétique Administration</h2>
                    <p class="text-xs text-slate-500">Volumétrie globale sur la base de données</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                    Ouvrir le tableau de bord complet &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/60">
                    <div class="text-xs font-semibold uppercase text-slate-500">Agences</div>
                    <div class="text-2xl font-bold text-slate-900 mt-1 font-mono">{{ \App\Models\Pressing::count() }}</div>
                </div>
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/60">
                    <div class="text-xs font-semibold uppercase text-slate-500">Utilisateurs</div>
                    <div class="text-2xl font-bold text-slate-900 mt-1 font-mono">{{ \App\Models\User::count() }}</div>
                </div>
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/60">
                    <div class="text-xs font-semibold uppercase text-slate-500">Services</div>
                    <div class="text-2xl font-bold text-slate-900 mt-1 font-mono">{{ \App\Models\Service::count() }}</div>
                </div>
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/60">
                    <div class="text-xs font-semibold uppercase text-slate-500">Factures</div>
                    <div class="text-2xl font-bold text-slate-900 mt-1 font-mono">{{ \App\Models\Facture::count() }}</div>
                </div>
            </div>
        </div>
    @endif

    @if($user->isCaissier())
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Espace Guichet Caisse</h2>
                    <p class="text-xs text-slate-500">Accédez directement aux opérations du guichet</p>
                </div>
                <a href="{{ route('caisse.dashboard') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                    Accéder au guichet caisse &rarr;
                </a>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('caisse.depot') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                    + Enregistrer un nouveau dépôt
                </a>
                <a href="{{ route('caisse.retrait') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold rounded-xl transition">
                    🔄 Encaissement & Retrait client
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
