@extends('layouts.app', ['title' => 'Nouveau Dépôt de Linge'])

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">NOUVEAU DÉPÔT CLIENT</h1>
                <span class="px-3 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                    📍 {{ $pressing->nom }} ({{ $pressing->ville }})
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Enregistrement du bon de pressing & création du ticket de retrait
            </p>
        </div>
        <a href="{{ route('caisse.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition flex items-center gap-1">
            <span>&larr; Retour au tableau de bord</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
            <div class="font-semibold text-rose-900">Veuillez corriger les erreurs suivantes :</div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('caisse.depot.store') }}" id="depotForm" class="space-y-6" onsubmit="handleDepotSubmit(event)">
        @csrf

        <!-- Section 1 : Informations Client & Retrait -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-xs font-bold text-slate-900 uppercase tracking-wider">
                <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center text-xs">1</span>
                <span>Identification du Client & Délais de Restitution</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="space-y-1.5">
                    <label for="client_nom" class="block text-xs font-semibold text-slate-700">
                        Nom complet du Client <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="client_nom"
                        id="client_nom"
                        value="{{ old('client_nom') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-slate-400"
                        placeholder="Ex: Kouamé Koffi"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="client_telephone" class="block text-xs font-semibold text-slate-700">
                        Téléphone de contact <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="client_telephone"
                        id="client_telephone"
                        value="{{ old('client_telephone') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs font-mono focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-slate-400"
                        placeholder="Ex: +228 90 00 00 00"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="client_email" class="block text-xs font-semibold text-slate-700">
                        Adresse Email <span class="text-slate-400 font-normal">(Optionnelle)</span>
                    </label>
                    <input
                        type="email"
                        name="client_email"
                        id="client_email"
                        value="{{ old('client_email') }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-slate-400"
                        placeholder="Ex: client@exemple.com"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="date_retrait_prevue" class="block text-xs font-semibold text-slate-700">
                        Date prévue de retrait <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="date_retrait_prevue"
                        id="date_retrait_prevue"
                        value="{{ old('date_retrait_prevue', now()->addDays(2)->format('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs font-mono focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                    >
                </div>
            </div>
        </div>

        <!-- Section 2 : Sélection des Prestations -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-900 uppercase tracking-wider">
                    <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center text-xs">2</span>
                    <span>Détail des Vêtements & Services Sollicités</span>
                </div>
                <button
                    type="button"
                    id="btnAddLigne"
                    class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Ajouter une ligne</span>
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left border-collapse text-xs" id="tableLignes">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-semibold">
                            <th class="py-3 px-4 w-1/2">Prestation</th>
                            <th class="py-3 px-4 text-right w-1/6">Prix Unitaire</th>
                            <th class="py-3 px-4 text-center w-1/6">Quantité</th>
                            <th class="py-3 px-4 text-right w-1/6">Sous-Total</th>
                            <th class="py-3 px-4 text-center w-12">Action</th>
                        </tr>
                    </thead>
                    <tbody id="lignesContainer" class="divide-y divide-slate-100">
                        <!-- Dynamic rows will be inserted here -->
                    </tbody>
                </table>
            </div>

            <div id="noLignesAlert" class="py-8 text-center text-xs text-slate-400 hidden">
                Aucune prestation ajoutée. Cliquez sur "+ Ajouter une ligne" ci-dessus.
            </div>
        </div>

        <!-- Section 3 : Synthèse Financière (Règle 100% au retrait) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-xs font-bold text-slate-900 uppercase tracking-wider">
                <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center text-xs">3</span>
                <span>Synthèse Financière du Ticket</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total -->
                <div class="border border-slate-200/80 p-5 rounded-2xl bg-slate-50/60 space-y-1">
                    <div class="text-[10px] uppercase font-bold text-slate-500">Montant Total</div>
                    <div class="text-2xl font-mono font-extrabold text-slate-900">
                        <span id="displayTotal">0,00</span> <span class="text-xs font-semibold text-slate-500">FCFA</span>
                    </div>
                    <div class="text-[11px] text-slate-500 pt-1">Calculé sur les prestations saisies</div>
                </div>

                <!-- Acompte (Verrouillé à 0 FCFA) -->
                <div class="border border-slate-200/80 p-5 rounded-2xl bg-white space-y-1">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] uppercase font-bold text-slate-500">Acompte Perçu</div>
                        <span class="text-[9px] font-mono uppercase px-2 py-0.5 rounded-full bg-slate-900 text-white font-semibold">Verrouillé</span>
                    </div>
                    <div class="text-2xl font-mono font-extrabold text-slate-400">
                        0,00 <span class="text-xs font-semibold">FCFA</span>
                    </div>
                    <div class="text-[11px] text-slate-600 pt-1 font-medium">
                        Règle stricte : 0 FCFA d'acompte à la commande
                    </div>
                </div>

                <!-- Reste à Payer au Retrait -->
                <div class="border-2 border-indigo-600 p-5 rounded-2xl bg-indigo-50/30 space-y-1">
                    <div class="text-[10px] uppercase font-bold text-indigo-900">Reste à Payer au Retrait</div>
                    <div class="text-2xl font-mono font-extrabold text-indigo-700">
                        <span id="displayReste">0,00</span> <span class="text-xs font-semibold text-indigo-600">FCFA</span>
                    </div>
                    <div class="text-[11px] text-indigo-800 pt-1 font-medium">
                        100% exigible lors du retrait des vêtements
                    </div>
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 shrink-0"></span>
                    <span>Le ticket sera immédiatement généré et redirigé vers l'impression thermique 80mm.</span>
                </div>
                <div class="flex space-x-3 w-full sm:w-auto">
                    <a href="{{ route('caisse.dashboard') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 text-center transition flex-1 sm:flex-none">
                        Annuler
                    </a>
                    <button
                        type="submit"
                        id="btnSubmit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md shadow-indigo-600/20 transition flex-1 sm:flex-none cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span id="btnText">Valider & Imprimer le Reçu &rarr;</span>
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

<!-- Template & Script for Dynamic Rows and Financial Calculation -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const services = @json($services);
        const container = document.getElementById('lignesContainer');
        const btnAdd = document.getElementById('btnAddLigne');
        const displayTotal = document.getElementById('displayTotal');
        const displayReste = document.getElementById('displayReste');
        const noAlert = document.getElementById('noLignesAlert');

        let rowIndex = 0;

        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        }

        function calculateTotals() {
            let total = 0;
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

                total += subtotal;
            });

            displayTotal.textContent = formatMoney(total);
            displayReste.textContent = formatMoney(total);
        }

        function addRow(selectedServiceId = '', defaultQty = 1) {
            const tr = document.createElement('tr');
            tr.className = 'ligne-row hover:bg-slate-50/60 transition duration-150';

            let optionsHtml = '<option value="">-- Choisir une prestation --</option>';
            services.forEach(s => {
                const isSelected = String(s.id) === String(selectedServiceId) ? 'selected' : '';
                optionsHtml += `<option value="${s.id}" data-prix="${s.prix_unitaire}" ${isSelected}>${s.designation} (${formatMoney(s.prix_unitaire)} FCFA)</option>`;
            });

            tr.innerHTML = `
                <td class="py-3 px-4">
                    <select name="lignes[${rowIndex}][service_id]" required class="service-select w-full px-3 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-3 px-4 text-right font-mono text-slate-600 price-display">
                    0,00 FCFA
                </td>
                <td class="py-3 px-4 text-center">
                    <input type="number" name="lignes[${rowIndex}][quantite]" value="${defaultQty}" min="1" max="500" required class="qty-input w-20 px-2.5 py-2 text-center bg-slate-50/50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition">
                </td>
                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 subtotal-display">
                    0,00 FCFA
                </td>
                <td class="py-3 px-4 text-center">
                    <button type="button" class="btn-remove w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition font-bold text-base flex items-center justify-center mx-auto">
                        &times;
                    </button>
                </td>
            `;

            container.appendChild(tr);

            // Bind events for recalculation
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
                    alert('Le dépôt doit contenir au moins une prestation.');
                }
            });

            rowIndex++;
            calculateTotals();
        }

        btnAdd.addEventListener('click', function () {
            addRow();
        });

        // Initialize with 1 empty row (or first available service)
        if (services.length > 0) {
            addRow(services[0].id, 1);
        } else {
            addRow();
        }

        // Validate before submit
        document.getElementById('depotForm').addEventListener('submit', function (e) {
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
            text.textContent = 'Traitement en cours...';
            spinner.classList.remove('hidden');
        }
    }
</script>
@endsection
