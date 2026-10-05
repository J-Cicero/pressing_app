<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pressing App' }} - Gestion de Pressing Multi-Agences</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50/80">
    <div class="min-h-screen flex flex-col md:flex-row">
        
        @auth
        <!-- Navigation Sidebar (Desktop & Mobile) -->
        <aside class="w-full md:w-64 bg-slate-900 text-slate-300 border-b md:border-b-0 md:border-r border-slate-800 flex flex-col justify-between shrink-0 md:sticky md:top-0 md:h-screen z-30 print:hidden shadow-xl">
            <div>
                <!-- Brand Header -->
                <div class="p-6 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('caisse.dashboard') }}" class="block">
                        <div class="text-xl font-extrabold tracking-tight text-white">
                            PRESSING<span class="text-indigo-400 font-light">APP</span>
                        </div>
                        <div class="text-[10px] uppercase tracking-widest text-slate-400 font-semibold mt-0.5">
                            Multi-Agences Suite
                        </div>
                    </a>
                </div>

                <!-- User Profile Card -->
                <div class="p-4 bg-slate-800/60 border border-slate-700/50 m-3 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-300 flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] uppercase font-mono text-indigo-300 tracking-wider">
                                {{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Caissier' }}
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->pressing)
                        <div class="mt-2 text-[10px] uppercase tracking-wider font-medium px-2.5 py-1 bg-slate-950/70 text-slate-300 border border-slate-700/60 rounded-lg block truncate">
                            📍 {{ auth()->user()->pressing->nom }}
                        </div>
                    @endif
                </div>

                <!-- Navigation Links -->
                <nav class="px-3 py-2 space-y-1">
                    @if(auth()->user()->isAdmin())
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            Espace Administration
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="text-base">📊</span> Tableau de bord
                        </a>
                        <a href="{{ route('admin.pressings.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('admin.pressings.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="text-base">🏢</span> Pressings / Agences
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="text-base">👥</span> Personnel & Caissiers
                        </a>
                        <a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('admin.services.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="text-base">🏷️</span> Grille Tarifaire
                        </a>
                        <a href="{{ route('admin.factures.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('admin.factures.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="text-base">🧾</span> Vue Factures
                        </a>
                    @elseif(auth()->user()->isCaissier())
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            Espace Guichet Caisse
                        </div>
                        <a href="{{ route('caisse.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('caisse.dashboard') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="text-base">📈</span> Dashboard Caisse
                        </a>
                        <a href="{{ route('caisse.depot') }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('caisse.depot*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="text-base">➕</span> Nouveau Dépôt
                        </a>
                        <a href="{{ route('caisse.retrait') }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('caisse.retrait*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="text-base">🔄</span> Retraits & Encaissement
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Logout Section -->
            <div class="p-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-3 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-300 transition text-center flex items-center justify-center gap-2 group cursor-pointer">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>
        @endauth

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-medium text-emerald-800 flex items-center gap-3 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->has('error'))
                        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-medium text-rose-800 flex items-center gap-3 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>{{ $errors->first('error') }}</span>
                        </div>
                    @endif
                </div>

                @yield('content')
            </main>

            <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-500 mt-auto print:hidden">
                &copy; {{ date('Y') }} PressingApp &bull; Solution de Gestion Multi-Agences & Blanchisserie
            </footer>
        </div>
    </div>
</body>
</html>
