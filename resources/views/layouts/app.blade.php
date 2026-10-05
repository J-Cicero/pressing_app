<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-950 text-zinc-100 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pressing App' }} - Gestion de Pressing Multi-Agences</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-zinc-100 bg-zinc-950 text-xs selection:bg-zinc-800 selection:text-white">
    <div class="min-h-screen flex flex-col md:flex-row">
        
        @auth
        <!-- Monochrome Navigation Sidebar (Desktop & Mobile) -->
        <aside class="w-full md:w-56 bg-zinc-900 text-zinc-300 border-b md:border-b-0 md:border-r border-zinc-800/80 flex flex-col justify-between shrink-0 md:sticky md:top-0 md:h-screen z-30 print:hidden shadow-xl">
            <div>
                <!-- Brand Header -->
                <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('caisse.dashboard') }}" class="block">
                        <div class="text-base font-extrabold tracking-tight text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-white inline-block"></span>
                            <span>PRESSING<span class="text-zinc-400 font-light">APP</span></span>
                        </div>
                        <div class="text-[9px] uppercase tracking-widest text-zinc-500 font-semibold mt-0.5">
                            Multi-Agences Suite
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
                            Agence : {{ auth()->user()->pressing->nom }}
                        </div>
                    @endif
                </div>

                <!-- Navigation Links with Monochrome Vector SVG Icons -->
                <nav class="px-2 py-1 space-y-0.5">
                    @if(auth()->user()->isAdmin())
                        <div class="px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-zinc-500">
                            Espace Administration
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-zinc-950' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Tableau de bord</span>
                        </a>
                        <a href="{{ route('admin.pressings.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.pressings.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('admin.pressings.*') ? 'text-zinc-950' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"/>
                            </svg>
                            <span>Pressings / Agences</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-zinc-950' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Personnel & Caissiers</span>
                        </a>
                        <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.services.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('admin.services.*') ? 'text-zinc-950' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span>Grille Tarifaire</span>
                        </a>
                        <a href="{{ route('admin.factures.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('admin.factures.*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('admin.factures.*') ? 'text-zinc-950' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Vue Factures</span>
                        </a>
                    @elseif(auth()->user()->isCaissier())
                        <div class="px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-zinc-500">
                            Espace Guichet Caisse
                        </div>
                        <a href="{{ route('caisse.dashboard') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('caisse.dashboard') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('caisse.dashboard') ? 'text-zinc-950' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Dashboard Caisse</span>
                        </a>
                        <a href="{{ route('caisse.depot') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('caisse.depot*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('caisse.depot*') ? 'text-zinc-950' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Nouveau Dépôt</span>
                        </a>
                        <a href="{{ route('caisse.retrait') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] font-medium rounded-lg transition {{ request()->routeIs('caisse.retrait*') ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/80' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('caisse.retrait*') ? 'text-zinc-950' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Retraits & Encaissement</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Logout Section -->
            <div class="p-2.5 border-t border-zinc-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-1.5 px-2.5 text-[11px] font-semibold rounded-lg bg-zinc-800/80 hover:bg-zinc-100 hover:text-zinc-950 text-zinc-300 transition text-center flex items-center justify-center gap-2 group cursor-pointer border border-zinc-700/50">
                        <svg class="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-950 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>
        @endauth

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-zinc-950 text-zinc-100">
            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-3">
                    @if (session('status'))
                        <div class="mb-3 p-3 bg-zinc-900 border border-zinc-700 rounded-xl text-xs font-medium text-zinc-200 flex items-center gap-2 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-white"></span>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->has('error'))
                        <div class="mb-3 p-3 bg-zinc-900 border border-zinc-700 rounded-xl text-xs font-medium text-zinc-200 flex items-center gap-2 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                            <span>{{ $errors->first('error') }}</span>
                        </div>
                    @endif
                </div>

                @yield('content')
            </main>

            <footer class="bg-zinc-950 border-t border-zinc-800/80 py-3.5 px-6 text-center text-[10px] text-zinc-500 mt-auto print:hidden">
                &copy; {{ date('Y') }} PressingApp &bull; Solution de Gestion Multi-Agences & Blanchisserie
            </footer>
        </div>
    </div>
</body>
</html>
