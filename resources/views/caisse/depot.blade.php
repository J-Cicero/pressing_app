@extends('layouts.app', ['title' => 'Nouveau Dépôt de Linge'])

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-[#374151]/20 gap-2">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#000]">NOUVEAU DÉPÔT CLIENT</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-0.5">
                Agence : {{ $pressing->nom }} ({{ $pressing->ville }}) &bull; Enregistrement du bon de pressing
            </p>
        </div>
        <a href="{{ route('caisse.dashboard') }}" class="text-xs font-semibold uppercase tracking-wider text-[#374151] hover:underline">
            &larr; Retour au tableau de bord
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-[#FFF] border-l-4 border-[#000] text-xs text-[#000] shadow-sm">
            <div class="font-bold mb-1">Veuillez corriger les erreurs suivantes :</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('caisse.depot.store') }}" id="depotForm" class="space-y-6">
        @csrf

        <!-- Section 1 : Informations Client & Retrait -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#000] mb-4 pb-2 border-b border-[#374151]/10">
                1. Identification du Client & Délais
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="client_nom" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                        Nom complet du Client *
                    </label>
                    <input
                        type="text"
                        name="client_nom"
                        id="client_nom"
                        value="{{ old('client_nom') }}"
                        required
                        class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                        placeholder="Ex: Kouamé Koffi"
                    >
                </div>

                <div>
                    <label for="client_telephone" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                        Téléphone de contact *
                    </label>
                    <input
                        type="text"
                        name="client_telephone"
                        id="client_telephone"
                        value="{{ old('client_telephone') }}"
                        required
                        class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs font-mono text-[#000] focus:outline-none focus:border-[#000]"
                        placeholder="Ex: +229 97 00 00 00"
                    >
                </div>

                <div>
                    <label for="date_retrait_prevue" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                        Date prévue de retrait *
                    </label>
                    <input
                        type="date"
                        name="date_retrait_prevue"
                        id="date_retrait_prevue"
                        value="{{ old('date_retrait_prevue', now()->addDays(2)->format('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}"
                        required
                        class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs font-mono text-[#000] focus:outline-none focus:border-[#000]"
                    >
                </div>
            </div>
        </div>

        <!-- Section 2 : Sélection des Prestations -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-[#374151]/10">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#000]">
                    2. Détail des Vêtements & Prestations
                </h2>
                <button
                    type="button"
                    id="btnAddLigne"
                    class="px-3 py-1.5 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition"
                >
                    + Ajouter une ligne
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs" id="tableLignes">
                    <thead>
                        <tr class="bg-[#F3F4F6] border-b border-[#374151]/20 text-[10px] uppercase tracking-wider text-[#374151]">
                            <th class="py-2.5 px-3 font-semibold w-1/2">Prestation</th>
                            <th class="py-2.5 px-3 font-semibold text-right w-1/6">Prix Unitaire</th>
                            <th class="py-2.5 px-3 font-semibold text-center w-1/6">Quantité</th>
                            <th class="py-2.5 px-3 font-semibold text-right w-1/6">Sous-Total</th>
                            <th class="py-2.5 px-3 text-center w-12">Action</th>
                        </tr>
                    </thead>
                    <tbody id="lignesContainer" class="divide-y divide-[#374151]/10">
                        <!-- Dynamic rows will be inserted here -->
                    </tbody>
                </table>
            </div>

            <div id="noLignesAlert" class="py-6 text-center text-xs text-[#374151] hidden">
                Aucune prestation ajoutée. Cliquez sur "+ Ajouter une ligne" ci-dessus.
            </div>
        </div>

        <!-- Section 3 : Synthèse Financière (Règle 100% au retrait) -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#000] mb-4 pb-2 border-b border-[#374151]/10">
                3. Synthèse Financière du Ticket
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total TTC -->
                <div class="border border-[#374151]/20 p-4 bg-[#F3F4F6]">
                    <div class="text-[10px] uppercase font-bold text-[#374151]">Montant Total TTC</div>
                    <div class="mt-2 text-2xl font-mono font-extrabold text-[#000]">
                        <span id="displayTotal">0,00</span> <span class="text-xs font-normal">FCFA</span>
                    </div>
                    <div class="text-[11px] text-[#374151] mt-1">Calculé sur les prestations saisies</div>
                </div>

                <!-- Acompte (Verrouillé à 0 FCFA) -->
                <div class="border border-[#374151]/20 p-4 bg-[#FFF]">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] uppercase font-bold text-[#374151]">Acompte Perçu</div>
                        <span class="text-[9px] font-mono uppercase px-1.5 py-0.5 bg-[#000] text-[#FFF]">Verrouillé</span>
                    </div>
                    <div class="mt-2 text-2xl font-mono font-extrabold text-[#374151]">
                        0,00 <span class="text-xs font-normal">FCFA</span>
                    </div>
                    <div class="text-[11px] text-[#374151] mt-1 font-semibold">
                        Règle stricte : 0 FCFA d'acompte à la commande
                    </div>
                </div>

                <!-- Reste à Payer au Retrait -->
                <div class="border-2 border-[#000] p-4 bg-[#FFF]">
                    <div class="text-[10px] uppercase font-bold text-[#000]">Reste à Payer au Retrait</div>
                    <div class="mt-2 text-2xl font-mono font-extrabold text-[#000]">
                        <span id="displayReste">0,00</span> <span class="text-xs font-normal">FCFA</span>
                    </div>
                    <div class="text-[11px] text-[#000] mt-1 font-medium">
                        100% exigible lors du retrait des vêtements
                    </div>
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="mt-6 pt-4 border-t border-[#374151]/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-[#374151]">
                    &bull; Le ticket sera immédiatement enregistré et dirigé vers l'impression thermique 80mm.
                </div>
                <div class="flex space-x-3 w-full sm:w-auto">
                    <a href="{{ route('caisse.dashboard') }}" class="px-4 py-2.5 border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider text-[#374151] hover:bg-[#F3F4F6] text-center transition flex-1 sm:flex-none">
                        Annuler
                    </a>
                    <button
                        type="submit"
                        id="btnSubmit"
                        class="px-6 py-2.5 bg-[#000] text-[#FFF] text-xs font-bold uppercase tracking-widest hover:bg-[#374151] transition flex-1 sm:flex-none"
                    >
                        Valider & Imprimer le Reçu &rarr;
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
            tr.className = 'ligne-row hover:bg-[#F3F4F6]/50 transition';

            let optionsHtml = '<option value="">-- Choisir une prestation --</option>';
            services.forEach(s => {
                const isSelected = String(s.id) === String(selectedServiceId) ? 'selected' : '';
                optionsHtml += `<option value="${s.id}" data-prix="${s.prix_unitaire}" ${isSelected}>${s.designation} (${formatMoney(s.prix_unitaire)} FCFA)</option>`;
            });

            tr.innerHTML = `
                <td class="py-3 px-3">
                    <select name="lignes[${rowIndex}][service_id]" required class="service-select w-full px-2 py-1.5 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-3 px-3 text-right font-mono text-[#374151] price-display">
                    0,00 FCFA
                </td>
                <td class="py-3 px-3 text-center">
                    <input type="number" name="lignes[${rowIndex}][quantite]" value="${defaultQty}" min="1" max="500" required class="qty-input w-20 px-2 py-1.5 text-center bg-[#FFF] border border-[#374151]/30 text-xs font-mono text-[#000] focus:outline-none focus:border-[#000]">
                </td>
                <td class="py-3 px-3 text-right font-mono font-bold text-[#000] subtotal-display">
                    0,00 FCFA
                </td>
                <td class="py-3 px-3 text-center">
                    <button type="button" class="btn-remove px-2 py-1 text-[11px] font-bold text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]">
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
</script>
@endsection
