<!DOCTYPE html>
<html lang="fr" class="h-full bg-[#F3F4F6]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pressing App' }} - Gestion de Pressing Multi-Agences</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-[#000] bg-[#F3F4F6]">
    <div class="min-h-screen flex flex-col md:flex-row bg-[#F3F4F6]">
        
        <!-- Main Content Area (On the Left) -->
        <div class="flex-1 flex flex-col min-w-0">
            @auth
            <!-- Mobile Header Bar (Only visible on small screens) -->
            <div class="md:hidden bg-[#FFF] border-b border-[#E5E7EB] p-4 flex items-center justify-between shadow-sm print:hidden">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('caisse.dashboard') }}" class="text-lg font-bold tracking-tight text-[#000]">
                    PRESSING<span class="text-[#374151] font-light">APP</span>
                </a>
                <div class="text-xs font-semibold uppercase tracking-wider px-2 py-1 bg-[#F3F4F6] text-[#374151] border border-[#E5E7EB]">
                    {{ auth()->user()->role === 'admin' ? 'Admin' : 'Caissier' }}
                </div>
            </div>
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

            <footer class="bg-[#FFF] border-t border-[#E5E7EB] py-4 px-6 text-center text-xs text-[#374151] mt-auto print:hidden">
                &copy; {{ date('Y') }} PressingApp &bull; Système Multi-Agences Monochrome Strict
            </footer>
        </div>

        @auth
        <!-- Navigation Sidebar (On the RIGHT Side) -->
        <aside class="w-full md:w-64 bg-[#FFF] border-t md:border-t-0 md:border-l border-[#E5E7EB] shadow-md flex flex-col justify-between shrink-0 md:sticky md:top-0 md:h-screen print:hidden">
            <div>
                <!-- Brand Header -->
                <div class="p-6 border-b border-[#E5E7EB] hidden md:block">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('caisse.dashboard') }}" class="text-xl font-extrabold tracking-tight text-[#000] block">
                        PRESSING<span class="text-[#374151] font-light">APP</span>
                    </a>
                    <p class="text-[10px] uppercase tracking-widest text-[#374151] mt-1 font-semibold">
                        Gestion Multi-Agences
                    </p>
                </div>

                <!-- User Profile Card -->
                <div class="p-4 bg-[#F3F4F6]/60 border-b border-[#E5E7EB] m-3 rounded-none border">
                    <div class="text-xs font-bold text-[#000] truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] uppercase font-mono text-[#374151] tracking-wider mt-0.5">
                        Role: {{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Caissier' }}
                    </div>
                    @if(auth()->user()->pressing)
                        <div class="mt-2 text-[10px] uppercase tracking-wider font-semibold px-2 py-1 bg-[#FFF] text-[#000] border border-[#E5E7EB] inline-block truncate max-w-full">
                            📍 {{ auth()->user()->pressing->nom }}
                        </div>
                    @endif
                </div>

                <!-- Navigation Links -->
                <nav class="px-3 py-2 space-y-1">
                    @if(auth()->user()->isAdmin())
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-[#374151]">
                            Espace Administration
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-none transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#000] text-[#FFF] shadow-sm font-bold border-l-4 border-[#000]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                            📊 Dashboard
                        </a>
                        <a href="{{ route('admin.pressings.index') }}" class="flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-none transition {{ request()->routeIs('admin.pressings.*') ? 'bg-[#000] text-[#FFF] shadow-sm font-bold border-l-4 border-[#000]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                            🏢 Pressings / Agences
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-none transition {{ request()->routeIs('admin.users.*') ? 'bg-[#000] text-[#FFF] shadow-sm font-bold border-l-4 border-[#000]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                            👥 Personnel & Caissiers
                        </a>
                        <a href="{{ route('admin.services.index') }}" class="flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-none transition {{ request()->routeIs('admin.services.*') ? 'bg-[#000] text-[#FFF] shadow-sm font-bold border-l-4 border-[#000]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                            🏷️ Grille Tarifaire
                        </a>
                        <a href="{{ route('admin.factures.index') }}" class="flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-none transition {{ request()->routeIs('admin.factures.*') ? 'bg-[#000] text-[#FFF] shadow-sm font-bold border-l-4 border-[#000]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                            🧾 Vue Factures
                        </a>
                    @elseif(auth()->user()->isCaissier())
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-[#374151]">
                            Espace Guichet Caisse
                        </div>
                        <a href="{{ route('caisse.dashboard') }}" class="flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-none transition {{ request()->routeIs('caisse.dashboard') ? 'bg-[#000] text-[#FFF] shadow-sm font-bold border-l-4 border-[#000]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                            📈 Dashboard Caisse
                        </a>
                        <a href="{{ route('caisse.depot') }}" class="flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-none transition {{ request()->routeIs('caisse.depot*') ? 'bg-[#000] text-[#FFF] shadow-sm font-bold border-l-4 border-[#000]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                            ➕ Nouveau Dépôt
                        </a>
                        <a href="{{ route('caisse.retrait') }}" class="flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-none transition {{ request()->routeIs('caisse.retrait*') ? 'bg-[#000] text-[#FFF] shadow-sm font-bold border-l-4 border-[#000]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                            🔄 Retraits & Encaissement
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Logout Section at Bottom of Sidebar -->
            <div class="p-4 border-t border-[#E5E7EB]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 text-xs font-bold uppercase tracking-widest bg-[#000] text-[#FFF] hover:bg-[#1F2937] transition text-center shadow-sm">
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>
        @endauth
    </div>
</body>
</html>
