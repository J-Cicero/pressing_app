<!DOCTYPE html>
<html lang="fr" class="h-full bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ERP Pressing & Lavage' }} - Multi-Services Suite</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-zinc-50 dark:bg-zinc-950 text-xs selection:bg-zinc-900 selection:text-white dark:selection:bg-zinc-800">
    <div class="min-h-screen flex flex-col md:flex-row">
        
        @auth
        <!-- Navigation Sidebar (Positionnée à GAUCHE sur Desktop, masquée à l'impression) -->
        <aside class="w-full md:w-64 bg-zinc-900 dark:bg-zinc-900 text-zinc-300 border-b md:border-b-0 md:border-r border-zinc-800 flex flex-col justify-between shrink-0 md:sticky md:top-0 md:h-screen z-30 print:hidden shadow-xl">
            <div class="overflow-y-auto">
                <!-- Brand Header -->
                <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('caisse.dashboard') }}" class="block">
                        <div class="text-base font-extrabold tracking-tight text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-white inline-block"></span>
                            <span>ERP<span class="text-zinc-400 font-light">SUITE</span></span>
                        </div>
                        <div class="text-[9px] uppercase tracking-widest text-zinc-400 font-semibold mt-0.5">
                            Pressing & Lavage Auto
                        </div>
                    </a>
                </div>

                <!-- User Profile Card -->
                <div class="p-2.5 bg-zinc-950/70 border border-zinc-800 m-2.5 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-zinc-800 border border-zinc-700 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] font-bold text-white truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[9px] uppercase font-mono text-zinc-400 tracking-wider">
                                {{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Caissier' }}
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->pressing)
                        <div class="mt-2 text-[9px] uppercase tracking-wider font-semibold px-2 py-0.5 bg-zinc-900 text-zinc-300 border border-zinc-800 rounded-md block truncate">
                            {{ auth()->user()->pressing->isLavage() ? 'Station Lavage' : 'Agence Pressing' }} : {{ auth()->user()->pressing->nom }}
                        </div>
                    @endif
                </div>

                <!-- Navigation Links with Vector SVG Icons Grouped by Pole -->
                <nav class="px-2 py-1 space-y-3">
                    @if(auth()->user()->isAdmin())
                        <!-- Section: Vue Globale & Consolidation -->
                        <div class="space-y-0.5">
                            <div class="px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-zinc-500">
                                Consolidation Réseau
                            </div>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span>Tableau de bord</span>
                            </a>
                            <a href="{{ route('admin.clients.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.clients.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Clients Réseau</span>
                            </a>
                            <a href="{{ route('admin.fidelite.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.fidelite.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                <span>Règles de Fidélité</span>
                            </a>
                            <a href="{{ route('admin.factures.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.factures.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Factures Consolidées</span>
                            </a>
                        </div>

                        <!-- Section: Pôle Pressing & Blanchisserie -->
                        <div class="space-y-1">
                            <div class="px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-zinc-400 flex items-center justify-between border-t border-zinc-800/80 pt-2.5">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                    <span>Pôle Pressing</span>
                                </span>
                            </div>
                            <div class="space-y-0.5">
                                <a href="{{ route('admin.pressings.index', ['type_activite' => 'pressing']) }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ (request()->routeIs('admin.pressings.*') && request('type_activite') !== 'lavage') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"/>
                                    </svg>
                                    <span>Agences Pressing</span>
                                </a>
                                <a href="{{ route('admin.services.index', ['type_activite' => 'pressing']) }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ (request()->routeIs('admin.services.*') && request('type_activite') === 'pressing') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span>Prestations Pressing</span>
                                </a>
                            </div>
                        </div>

                        <!-- Section: Pôle Station Lavage Auto / Moto -->
                        <div class="space-y-1">
                            <div class="px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-zinc-400 flex items-center justify-between border-t border-zinc-800/80 pt-2.5">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                    <span>Pôle Lavage Auto/Moto</span>
                                </span>
                            </div>
                            <div class="space-y-0.5">
                                <a href="{{ route('admin.pressings.index', ['type_activite' => 'lavage']) }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ (request()->routeIs('admin.pressings.*') && request('type_activite') === 'lavage') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"/>
                                    </svg>
                                    <span>Stations de Lavage</span>
                                </a>
                                <a href="{{ route('admin.services.index', ['type_activite' => 'lavage']) }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ (request()->routeIs('admin.services.*') && request('type_activite') === 'lavage') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span>Prestations Lavage</span>
                                </a>
                                <a href="{{ route('admin.laveurs.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.laveurs.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Personnel Laveurs</span>
                                </a>
                                <a href="{{ route('admin.vehicules.echeances') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.vehicules.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Échéances Véhicules</span>
                                </a>
                            </div>
                        </div>

                        <!-- Section: Administration & Paramètres Réseau -->
                        <div class="space-y-1">
                            <div class="px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-zinc-500 border-t border-zinc-800/80 pt-2.5">
                                Paramètres Réseau
                            </div>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span>Caissiers & Gestionnaires</span>
                            </a>
                        </div>
                    @elseif(auth()->user()->isCaissier())
                        <div class="px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-zinc-500">
                            Espace Guichet Caisse
                        </div>
                        <a href="{{ route('caisse.dashboard') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('caisse.dashboard') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Dashboard Caisse</span>
                        </a>
                        <a href="{{ route('caisse.depot') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('caisse.depot*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>{{ auth()->user()->pressing?->isLavage() ? 'Nouveau Lavage Auto' : 'Nouveau Dépôt Linge' }}</span>
                        </a>
                        <a href="{{ route('caisse.retrait') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('caisse.retrait*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Retraits & Encaissement</span>
                        </a>
                        @if(auth()->user()->pressing?->isLavage())
                            <a href="{{ route('caisse.vehicules.echeances') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('caisse.vehicules.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Échéances Véhicules</span>
                            </a>
                            <a href="{{ route('caisse.laveurs.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('caisse.laveurs.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Laveurs & Commissions</span>
                            </a>
                        @endif
                    @endif
                </nav>
            </div>

            <!-- Theme Toggle & Logout Section -->
            <div class="p-2.5 border-t border-zinc-800 space-y-2">
                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleTheme()" class="w-full flex items-center justify-between px-2.5 py-1.5 text-[11px] font-semibold rounded-lg bg-zinc-800/80 hover:bg-zinc-700 text-zinc-300 transition cursor-pointer border border-zinc-700/50">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <span class="block dark:hidden">Mode Sombre</span>
                        <span class="hidden dark:block">Mode Clair</span>
                    </span>
                    <span class="text-[9px] font-mono uppercase px-1.5 py-0.5 rounded bg-zinc-950 text-zinc-400 font-bold">
                        <span class="block dark:hidden">OFF</span>
                        <span class="hidden dark:block">ON</span>
                    </span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-1.5 px-2.5 text-[11px] font-semibold rounded-lg bg-zinc-800/80 hover:bg-white hover:text-zinc-950 text-zinc-300 transition text-center flex items-center justify-center gap-2 group cursor-pointer border border-zinc-700/50">
                        <svg class="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-950 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>
        @endauth

        <!-- Main Content Area (Right on Desktop, Main view) -->
        <div class="flex-1 flex flex-col min-w-0 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-3">
                    @if (session('status'))
                        <div class="mb-3 p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-medium text-zinc-900 dark:text-zinc-100 flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-zinc-900 dark:bg-white"></span>
                                <span>{{ session('status') }}</span>
                            </div>
                            @if(session('whatsapp_url'))
                                <a href="{{ session('whatsapp_url') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-black text-white dark:bg-white dark:text-black font-bold text-[11px] rounded-lg transition hover:opacity-90">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span>Notifier WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    @if (session('whatsapp_pret_url'))
                        <div class="mb-3 p-3 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-white dark:bg-zinc-950"></span>
                                <span>Ticket prêt pour restitution. Envoyer la notification client ?</span>
                            </div>
                            <a href="{{ session('whatsapp_pret_url') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white text-zinc-950 dark:bg-zinc-950 dark:text-white font-bold text-[11px] rounded-lg transition hover:opacity-90">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span>Ouvrir WhatsApp</span>
                            </a>
                        </div>
                    @endif

                    @if (session('fidelite_message'))
                        <div class="mb-3 p-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-zinc-900 dark:bg-white"></span>
                                <span>{{ session('fidelite_message') }}</span>
                            </div>
                            @if(session('fidelite_url'))
                                <a href="{{ session('fidelite_url') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-black text-white dark:bg-white dark:text-black font-bold text-[11px] rounded-lg transition hover:opacity-90">
                                    <span>Féliciter sur WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    @if ($errors->has('error'))
                        <div class="mb-3 p-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl text-xs font-medium text-zinc-900 dark:text-zinc-100 flex items-center gap-2 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-black dark:bg-white"></span>
                            <span>{{ $errors->first('error') }}</span>
                        </div>
                    @endif
                </div>

                @yield('content')
            </main>

            <footer class="bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800/80 py-3 px-6 text-center text-[10px] text-zinc-500 dark:text-zinc-500 mt-auto print:hidden">
                &copy; {{ date('Y') }} ERP Multi-Services &bull; Pressing, Blanchisserie & Station de Lavage Auto/Moto
            </footer>
        </div>

    </div>

    <!-- Reusable Modal Confirmation & Alert -->
    <x-modal-confirm />

    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
</body>
</html>
