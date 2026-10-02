<!DOCTYPE html>
<html lang="fr" class="h-full bg-[#F3F4F6]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pressing App' }} - Gestion de Pressing Multi-Agences</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-[#000] bg-[#F3F4F6]">
    <div class="min-h-full flex flex-col">
        @auth
        <header class="bg-[#FFF] border-b border-[#374151]/20 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-6">
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="text-xl font-bold tracking-tight text-[#000]">
                            PRESSING<span class="text-[#374151] font-light">APP</span>
                        </a>

                        @if(auth()->user()->isAdmin())
                            <nav class="hidden md:flex space-x-1">
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.pressings.index') }}" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('admin.pressings.*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                                    Pressings
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('admin.users.*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                                    Personnel
                                </a>
                                <a href="{{ route('admin.services.index') }}" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('admin.services.*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                                    Prestations
                                </a>
                                <a href="{{ route('admin.factures.index') }}" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('admin.factures.*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                                    Factures
                                </a>
                            </nav>
                        @elseif(auth()->user()->isCaissier())
                            <nav class="hidden md:flex space-x-1">
                                <a href="{{ route('caisse.dashboard') }}" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('caisse.dashboard') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                                    Caisse Dashboard
                                </a>
                                <a href="{{ route('caisse.depot') }}" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('caisse.depot*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                                    + Nouveau Dépôt
                                </a>
                                <a href="{{ route('caisse.retrait') }}" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('caisse.retrait*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                                    Retraits & Encaissement
                                </a>
                            </nav>
                        @endif

                        @if(auth()->user()->pressing)
                            <span class="text-xs uppercase tracking-wider px-2 py-1 bg-[#F3F4F6] text-[#374151] border border-[#374151]/20">
                                {{ auth()->user()->pressing->nom }} ({{ auth()->user()->pressing->ville }})
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm font-medium text-[#000]">{{ auth()->user()->name }}</div>
                            <div class="text-xs uppercase tracking-wider text-[#374151]">
                                {{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Caissier' }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider bg-[#000] text-[#FFF] hover:bg-[#374151] transition">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <!-- Mobile navigation Admin -->
                    <div class="md:hidden flex overflow-x-auto space-x-2 py-2 border-t border-[#374151]/10">
                        <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151]' }}">Dashboard</a>
                        <a href="{{ route('admin.pressings.index') }}" class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('admin.pressings.*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151]' }}">Pressings</a>
                        <a href="{{ route('admin.users.index') }}" class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('admin.users.*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151]' }}">Personnel</a>
                        <a href="{{ route('admin.services.index') }}" class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('admin.services.*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151]' }}">Prestations</a>
                        <a href="{{ route('admin.factures.index') }}" class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('admin.factures.*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151]' }}">Factures</a>
                    </div>
                @elseif(auth()->user()->isCaissier())
                    <!-- Mobile navigation Caissier -->
                    <div class="md:hidden flex overflow-x-auto space-x-2 py-2 border-t border-[#374151]/10">
                        <a href="{{ route('caisse.dashboard') }}" class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('caisse.dashboard') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151]' }}">Dashboard</a>
                        <a href="{{ route('caisse.depot') }}" class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('caisse.depot*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151]' }}">+ Nouveau Dépôt</a>
                        <a href="{{ route('caisse.retrait') }}" class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('caisse.retrait*') ? 'bg-[#000] text-[#FFF]' : 'text-[#374151]' }}">Retraits & Caisse</a>
                    </div>
                @endif
            </div>
        </header>
        @endauth

        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                @if (session('status'))
                    <div class="mb-4 p-4 bg-[#FFF] border-l-4 border-[#000] text-xs font-medium text-[#000] shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->has('error'))
                    <div class="mb-4 p-4 bg-[#FFF] border-l-4 border-[#374151] text-xs font-medium text-[#000] shadow-sm">
                        {{ $errors->first('error') }}
                    </div>
                @endif
            </div>

            @yield('content')
        </main>

        <footer class="bg-[#FFF] border-t border-[#374151]/10 py-4 text-center text-xs text-[#374151]">
            &copy; {{ date('Y') }} PressingApp — Système Multi-Agences Monochrome Strict
        </footer>
    </div>
</body>
</html>
