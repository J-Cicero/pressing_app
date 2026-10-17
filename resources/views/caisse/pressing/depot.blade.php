@extends('layouts.app', ['title' => 'Nouveau Dépôt Vêtements - ' . $pressing->nom])

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header Pure Pressing -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white uppercase">
                    Nouveau Dépôt Vêtements & Linge
                </h1>
                <span class="px-3 py-0.5 text-xs font-bold rounded-full bg-black dark:bg-white text-white dark:text-black flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>{{ $pressing->nom }} ({{ $pressing->ville }})</span>
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Enregistrement des effets confiés, date convenue de retrait et génération du ticket client.
            </p>
        </div>
        <a href="{{ route('caisse.dashboard') }}" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white transition flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour au tableau de bord</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-2xl text-xs text-zinc-900 dark:text-white space-y-1">
            <div class="font-bold">Veuillez corriger les erreurs suivantes :</div>
            <ul class="list-disc list-inside space-y-0.5 text-zinc-600 dark:text-zinc-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('caisse.depot.store') }}" id="depotPressingForm" class="space-y-6" onsubmit="handleDepotSubmit(event)">
        @csrf

        <!-- Section 1 : Identification du Client (Priorité Email & Reconnaissance Automatique) -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-zinc-100 dark:border-zinc-800 text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">
                <span class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-xs font-mono">1</span>
                <span>Identification Client (Reconnaissance par Email ou Téléphone)</span>
            </div>

            <!-- Client Lookup feedback / banner -->
            <div id="clientLookupBanner" class="hidden p-4 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 transition">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-black text-white dark:bg-white dark:text-black flex items-center justify-center font-bold text-xs shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-950 dark:text-white" id="clientLookupName">Client reconnu</div>
                            <div class="text-[11px] text-zinc-500 dark:text-zinc-400" id="clientLookupVisites">0 visite(s) au pressing</div>
                        </div>
                    </div>
                    <div id="clientLoyaltyBadge" class="hidden">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-black text-white dark:bg-white dark:text-black" id="clientLoyaltyText">
                            Palier Fidélité
                        </span>
                    </div>
                </div>
            </div>

            <!-- Privilège Fidélité Débloqué (Optionnel à appliquer) -->
            <div id="loyaltyOfferCard" class="hidden p-4 rounded-2xl border-2 border-black dark:border-white bg-zinc-50 dark:bg-zinc-950 transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-black text-white dark:bg-white dark:text-black">
                                Récompense Fidélité Disponible
                            </span>
                            <span id="loyaltyQuotaBadge" class="text-xs font-mono font-bold text-zinc-700 dark:text-zinc-300"></span>
                        </div>
                        <div id="loyaltyDescription" class="text-xs font-bold text-zinc-950 dark:text-white"></div>
                        <div id="loyaltyExpiration" class="text-[11px] text-zinc-500 dark:text-zinc-400 font-medium"></div>
                    </div>
                    <label class="inline-flex items-center gap-2.5 cursor-pointer bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-4 py-2.5 rounded-xl shrink-0 hover:border-black dark:hover:border-white transition shadow-xs">
                        <input type="checkbox" id="toggleFidelite" class="w-4 h-4 rounded border-zinc-300 dark:border-zinc-700 text-black focus:ring-black dark:focus:ring-white">
                        <span class="text-xs font-bold text-zinc-950 dark:text-white">Appliquer la réduction</span>
                    </label>
                    <input type="hidden" name="client_fidelite_avantage_id" id="clientFideliteAvantageId" value="">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Email (Priorité Reconnaissance Unique) -->
                <div class="space-y-1.5">
                    <label for="client_email" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Email du Client <span class="text-zinc-400 dark:text-zinc-500 font-normal">(Recherche auto)</span>
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            name="client_email"
                            id="client_email"
                            value="{{ old('client_email') }}"
                            class="w-full pl-3.5 pr-8 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                            placeholder="client@exemple.com"
                            autocomplete="off"
                        >
                        <div id="emailSpinner" class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none hidden">
                            <svg class="animate-spin h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Téléphone -->
                <div class="space-y-1.5">
                    <label for="client_telephone" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Téléphone <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="client_telephone"
                            id="client_telephone"
                            value="{{ old('client_telephone') }}"
                            required
                            class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                            placeholder="Ex: 90000000"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <!-- Nom Complet -->
                <div class="space-y-1.5">
                    <label for="client_nom" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Nom complet du Client <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <input
                        type="text"
                        name="client_nom"
                        id="client_nom"
                        value="{{ old('client_nom') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                        placeholder="Ex: Koffi Mensah"
                    >
                </div>

                <!-- Date Prévue de Retrait (OBLIGATOIRE) -->
                <div class="space-y-1.5">
                    <label for="date_retrait_prevue" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Date prévue de Retrait <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <input
                        type="date"
                        name="date_retrait_prevue"
                        id="date_retrait_prevue"
                        value="{{ old('date_retrait_prevue', now()->addDays(2)->format('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                    >
                </div>
            </div>
        </div>

        <!-- Section 2 : Prestations Blanchisserie & Pressing de l'Agence -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center gap-2 text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">
                    <span class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-xs font-mono">2</span>
                    <span>Articles Confiés & Prestations de Blanchisserie</span>
                </div>
                <button
                    type="button"
                    id="btnAddLigne"
                    class="px-3.5 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Ajouter un article</span>
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
                <table class="w-full text-left border-collapse text-xs" id="tableLignes">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                            <th class="py-3 px-4 w-1/2">Prestation Textile</th>
                            <th class="py-3 px-4 text-right w-1/6">P.U (FCFA)</th>
                            <th class="py-3 px-4 text-center w-1/6">Quantité</th>
                            <th class="py-3 px-4 text-right w-1/6">Sous-Total</th>
                            <th class="py-3 px-4 text-center w-12">Action</th>
                        </tr>
                    </thead>
                    <tbody id="lignesContainer" class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        <!-- Dynamic rows -->
                    </tbody>
                </table>
            </div>

            <div id="noLignesAlert" class="py-8 text-center text-xs text-zinc-500 dark:text-zinc-400 hidden">
                Aucune prestation ajoutée. Cliquez sur "+ Ajouter un article" ci-dessus.
            </div>
        </div>

        <!-- Section 3 : Synthèse Financière du Dépôt (100% au Retrait) -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-zinc-100 dark:border-zinc-800 text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">
                <span class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-xs font-mono">3</span>
                <span>Synthèse Financière & Règlement</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Montant Brut -->
                <div class="border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-950 space-y-1">
                    <div class="text-[10px] uppercase font-bold text-zinc-500 dark:text-zinc-400">Montant Brut Articles</div>
                    <div class="text-2xl font-mono font-black text-zinc-950 dark:text-white">
                        <span id="displayTotal">0</span> <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">FCFA</span>
                    </div>
                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400 pt-1">Tarif ordinaire catalogué</div>
                </div>

                <!-- Remise Fidélité Déduite (Dynamique) -->
                <div id="cardRemiseFidelite" class="border border-zinc-300 dark:border-zinc-700 p-5 rounded-2xl bg-zinc-100 dark:bg-zinc-800/60 space-y-1 hidden">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] uppercase font-bold text-zinc-900 dark:text-white">Remise Fidélité</div>
                        <span class="text-[9px] font-mono uppercase px-2 py-0.5 rounded-full bg-black text-white dark:bg-white dark:text-black font-bold">Privilège</span>
                    </div>
                    <div class="text-2xl font-mono font-black text-zinc-950 dark:text-white">
                        <span id="displayRemise">0</span> <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">FCFA</span>
                    </div>
                    <div class="text-[11px] text-zinc-600 dark:text-zinc-400 pt-1 font-medium" id="displayRemiseLabel">Avantage déduit</div>
                </div>

                <!-- Acompte (0 FCFA) -->
                <div class="border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-950 space-y-1">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] uppercase font-bold text-zinc-500 dark:text-zinc-400">Acompte au Dépôt</div>
                        <span class="text-[9px] font-mono uppercase px-2 py-0.5 rounded-full bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold border border-zinc-300 dark:border-zinc-700">0 FCFA</span>
                    </div>
                    <div class="text-2xl font-mono font-black text-zinc-400 dark:text-zinc-500">
                        0 <span class="text-xs font-semibold">FCFA</span>
                    </div>
                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400 pt-1 font-medium">
                        Règle stricte : Aucun acompte
                    </div>
                </div>

                <!-- Net à Payer au Retrait -->
                <div class="border-2 border-black dark:border-white p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-950 space-y-1">
                    <div class="text-[10px] uppercase font-bold text-zinc-950 dark:text-white">Net à Payer au Retrait</div>
                    <div class="text-2xl font-mono font-black text-zinc-950 dark:text-white">
                        <span id="displayReste">0</span> <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">FCFA</span>
                    </div>
                    <div class="text-[11px] text-zinc-600 dark:text-zinc-300 pt-1 font-medium">
                        100% exigible à la récupération
                    </div>
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-black dark:bg-white shrink-0"></span>
                    <span>Ticket thermique 80mm avec rappel automatique par e-mail le jour du retrait.</span>
                </div>
                <div class="flex space-x-3 w-full sm:w-auto">
                    <a href="{{ route('caisse.dashboard') }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-center transition flex-1 sm:flex-none">
                        Annuler
                    </a>
                    <button
                        type="submit"
                        id="btnSubmit"
                        class="px-6 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition flex-1 sm:flex-none cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span id="btnText">Créer le Dépôt & Imprimer Ticket &rarr;</span>
                        <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript for Dynamic Rows, Totals, and Instant Email/Phone Lookup -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const services = @json($services);
        const container = document.getElementById('lignesContainer');
        const btnAdd = document.getElementById('btnAddLigne');
        // Financial Elements
        const displayTotal = document.getElementById('displayTotal');
        const displayReste = document.getElementById('displayReste');
        const cardRemiseFidelite = document.getElementById('cardRemiseFidelite');
        const displayRemise = document.getElementById('displayRemise');
        const displayRemiseLabel = document.getElementById('displayRemiseLabel');
        const noAlert = document.getElementById('noLignesAlert');

        // Lookup & Loyalty Elements
        const emailInput = document.getElementById('client_email');
        const phoneInput = document.getElementById('client_telephone');
        const nameInput = document.getElementById('client_nom');
        const emailSpinner = document.getElementById('emailSpinner');
        const lookupBanner = document.getElementById('clientLookupBanner');
        const lookupName = document.getElementById('clientLookupName');
        const lookupVisites = document.getElementById('clientLookupVisites');
        const loyaltyBadge = document.getElementById('clientLoyaltyBadge');
        const loyaltyText = document.getElementById('clientLoyaltyText');
        const loyaltyOfferCard = document.getElementById('loyaltyOfferCard');
        const loyaltyQuotaBadge = document.getElementById('loyaltyQuotaBadge');
        const loyaltyDescription = document.getElementById('loyaltyDescription');
        const loyaltyExpiration = document.getElementById('loyaltyExpiration');
        const toggleFidelite = document.getElementById('toggleFidelite');
        const clientFideliteAvantageId = document.getElementById('clientFideliteAvantageId');

        let rowIndex = 0;
        let activeAvantage = null;

        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        function calculateTotals() {
            let grossTotal = 0;
            const rows = container.querySelectorAll('tr.ligne-row');

            if (rows.length === 0) {
                noAlert.classList.remove('hidden');
            } else {
                noAlert.classList.add('hidden');
            }

            rows.forEach(row => {
                const select = row.querySelector('.service-select');
                const qtyInput = row.querySelector('.qty-input');
                const subtotalEl = row.querySelector('.subtotal-display');
                const priceEl = row.querySelector('.price-display');

                const selectedOption = select.options[select.selectedIndex];
                const price = selectedOption ? parseFloat(selectedOption.dataset.prix || 0) : 0;
                const qty = parseInt(qtyInput.value) || 0;
                const subtotal = price * qty;

                priceEl.textContent = formatMoney(price) + ' FCFA';
                subtotalEl.textContent = formatMoney(subtotal) + ' FCFA';

                grossTotal += subtotal;
            });

            // Loyalty discount calculation
            let remise = 0;
            if (toggleFidelite && toggleFidelite.checked && activeAvantage) {
                if (activeAvantage.type_avantage === 'pourcentage') {
                    remise = Math.round(grossTotal * (activeAvantage.valeur_avantage / 100));
                } else if (activeAvantage.type_avantage === 'service_offert') {
                    remise = grossTotal;
                } else if (activeAvantage.type_avantage === 'montant_fixe') {
                    remise = Math.min(grossTotal, activeAvantage.valeur_avantage);
                }
            }

            const netTotal = Math.max(0, grossTotal - remise);

            displayTotal.textContent = formatMoney(grossTotal);

            if (cardRemiseFidelite) {
                if (remise > 0) {
                    displayRemise.textContent = '-' + formatMoney(remise);
                    if (displayRemiseLabel && activeAvantage) {
                        displayRemiseLabel.textContent = activeAvantage.description;
                    }
                    cardRemiseFidelite.classList.remove('hidden');
                } else {
                    cardRemiseFidelite.classList.add('hidden');
                }
            }

            displayReste.textContent = formatMoney(netTotal);
        }

        if (toggleFidelite) {
            toggleFidelite.addEventListener('change', calculateTotals);
        }

        function addRow(selectedServiceId = '', defaultQty = 1) {
            const tr = document.createElement('tr');
            tr.className = 'ligne-row hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition duration-150';

            let optionsHtml = '<option value="">-- Choisir une prestation pressing --</option>';
            services.forEach(s => {
                const isSelected = String(s.id) === String(selectedServiceId) ? 'selected' : '';
                optionsHtml += `<option value="${s.id}" data-prix="${s.prix_unitaire}" ${isSelected}>${s.designation} (${formatMoney(s.prix_unitaire)} FCFA)</option>`;
            });

            tr.innerHTML = `
                <td class="py-3 px-4">
                    <select name="lignes[${rowIndex}][service_id]" required class="service-select w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:outline-none focus:border-black dark:focus:border-white transition">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-3 px-4 text-right font-mono text-zinc-600 dark:text-zinc-400 price-display">
                    0 FCFA
                </td>
                <td class="py-3 px-4 text-center">
                    <input type="number" name="lignes[${rowIndex}][quantite]" value="${defaultQty}" min="1" max="500" required class="qty-input w-20 px-2.5 py-2 text-center bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white focus:outline-none focus:border-black dark:focus:border-white transition">
                </td>
                <td class="py-3 px-4 text-right font-mono font-bold text-zinc-900 dark:text-white subtotal-display">
                    0 FCFA
                </td>
                <td class="py-3 px-4 text-center">
                    <button type="button" class="btn-remove w-7 h-7 rounded-lg text-zinc-400 dark:text-zinc-500 hover:text-black dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition font-bold text-base flex items-center justify-center mx-auto cursor-pointer">
                        &times;
                    </button>
                </td>
            `;

            container.appendChild(tr);

            const selectEl = tr.querySelector('.service-select');
            const qtyEl = tr.querySelector('.qty-input');
            const btnRemove = tr.querySelector('.btn-remove');

            selectEl.addEventListener('change', calculateTotals);
            qtyEl.addEventListener('input', calculateTotals);
            btnRemove.addEventListener('click', function () {
                if (container.querySelectorAll('tr.ligne-row').length > 1) {
                    tr.remove();
                    calculateTotals();
                } else {
                    alert('Le dépôt doit comporter au moins un article.');
                }
            });

            rowIndex++;
            calculateTotals();
        }

        btnAdd.addEventListener('click', function () {
            addRow();
        });

        if (services.length > 0) {
            addRow(services[0].id, 1);
        } else {
            addRow();
        }

        // Debounced Client Lookup function
        let lookupTimer = null;
        function performClientLookup(paramName, paramValue) {
            clearTimeout(lookupTimer);
            if (!paramValue || paramValue.trim().length < 3) {
                lookupBanner.classList.add('hidden');
                if (loyaltyOfferCard) loyaltyOfferCard.classList.add('hidden');
                activeAvantage = null;
                if (toggleFidelite) toggleFidelite.checked = false;
                if (clientFideliteAvantageId) clientFideliteAvantageId.value = '';
                calculateTotals();
                return;
            }

            lookupTimer = setTimeout(() => {
                if (emailSpinner) emailSpinner.classList.remove('hidden');

                fetch(`{{ route('caisse.clients.lookup') }}?${paramName}=${encodeURIComponent(paramValue.trim())}`)
                    .then(res => res.json())
                    .then(data => {
                        if (emailSpinner) emailSpinner.classList.add('hidden');
                        if (data.found && data.client) {
                            if (!nameInput.value || nameInput.value.trim().length === 0) {
                                nameInput.value = data.client.nom;
                            }
                            if (paramName === 'email' && data.client.telephone) {
                                phoneInput.value = data.client.telephone;
                            }
                            if (paramName === 'telephone' && data.client.email && (!emailInput.value || emailInput.value.trim().length === 0)) {
                                emailInput.value = data.client.email;
                            }

                            lookupName.textContent = `Client reconnu : ${data.client.nom}`;
                            lookupVisites.textContent = `${data.client.visites_pressing} visite(s) au pressing (${data.client.total_visites} au total)`;
                            lookupBanner.classList.remove('hidden');

                            if (data.fidelite) {
                                loyaltyBadge.classList.remove('hidden');
                                loyaltyText.textContent = `Avantage : ${data.fidelite.description}`;
                            } else {
                                loyaltyBadge.classList.add('hidden');
                            }

                            // Active Loyalty Rewards (Advantages)
                            if (data.avantages && data.avantages.length > 0) {
                                activeAvantage = data.avantages[0];
                                loyaltyDescription.textContent = activeAvantage.description;
                                loyaltyQuotaBadge.textContent = `${activeAvantage.utilisations_restantes} utilisation(s) restante(s)`;
                                loyaltyExpiration.textContent = activeAvantage.date_expiration ? `Valable jusqu'au ${activeAvantage.date_expiration}` : 'Validité permanente';
                                clientFideliteAvantageId.value = activeAvantage.id;
                                toggleFidelite.checked = true; // Auto-checked for convenience, user can uncheck
                                loyaltyOfferCard.classList.remove('hidden');
                            } else {
                                activeAvantage = null;
                                loyaltyOfferCard.classList.add('hidden');
                                toggleFidelite.checked = false;
                                clientFideliteAvantageId.value = '';
                            }

                            calculateTotals();
                        } else {
                            lookupBanner.classList.add('hidden');
                            if (loyaltyOfferCard) loyaltyOfferCard.classList.add('hidden');
                            activeAvantage = null;
                            if (toggleFidelite) toggleFidelite.checked = false;
                            if (clientFideliteAvantageId) clientFideliteAvantageId.value = '';
                            calculateTotals();
                        }
                    })
                    .catch(() => {
                        if (emailSpinner) emailSpinner.classList.add('hidden');
                    });
            }, 300);
        }

        // Trigger on Email Input
        if (emailInput) {
            emailInput.addEventListener('input', function () {
                performClientLookup('email', this.value);
            });
        }

        // Trigger on Phone Input as fallback
        if (phoneInput) {
            phoneInput.addEventListener('input', function () {
                if (!emailInput.value || emailInput.value.trim().length === 0) {
                    performClientLookup('telephone', this.value);
                }
            });
        }

        // Validation submit
        document.getElementById('depotPressingForm').addEventListener('submit', function (e) {
            const rows = container.querySelectorAll('tr.ligne-row');
            if (rows.length === 0) {
                e.preventDefault();
                alert('Veuillez ajouter au moins une prestation.');
                return;
            }

            let valid = true;
            rows.forEach(r => {
                const s = r.querySelector('.service-select');
                if (!s.value) {
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Veuillez sélectionner une prestation valide pour chaque ligne.');
            }
        });
    });

    function handleDepotSubmit(e) {
        const btn = document.getElementById('btnSubmit');
        const text = document.getElementById('btnText');
        const spinner = document.getElementById('btnSpinner');

        if (btn) {
            text.textContent = 'Génération du ticket...';
            spinner.classList.remove('hidden');
        }
    }
</script>
@endsection
