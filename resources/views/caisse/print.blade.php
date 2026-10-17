@php
    $whatsApp = app(\App\Services\WhatsAppNotificationService::class);
    $whatsappUrl = $whatsApp->urlDepot($facture);
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $facture->num_ticket }}</title>
    <style>
        /* Format strict pour imprimante thermique 80mm */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000000;
            background-color: #f4f4f5;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Barre d'outils écran (Monochrome strict, masquée à l'impression) */
        .toolbar-wrapper {
            max-width: 480px;
            margin: 16px auto 12px;
            padding: 0 12px;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            background: #ffffff;
            padding: 8px 12px;
            border-radius: 12px;
            border: 1px solid #e4e4e7;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 12px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-print {
            background: #000000;
            color: #ffffff;
            flex: 1;
        }

        .btn-print:hover {
            background: #27272a;
        }

        .btn-whatsapp {
            background: #27272a;
            color: #ffffff;
        }

        .btn-whatsapp:hover {
            background: #000000;
        }

        .btn-back {
            background: #f4f4f5;
            color: #18181b;
            border: 1px solid #d4d4d8;
        }

        .btn-back:hover {
            background: #e4e4e7;
        }

        .print-tip {
            margin-top: 8px;
            font-size: 10px;
            color: #71717a;
            text-align: center;
            line-height: 1.3;
        }

        /* Simulation Rouleau Thermique 80mm */
        .ticket-wrapper {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto 24px;
            background: #ffffff;
            padding: 4mm 3mm;
            border: 1px solid #d4d4d8;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .title {
            font-size: 14px;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .ticket-num {
            font-size: 15px;
            margin: 6px 0;
            padding: 4px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .double-divider {
            border-top: 2px solid #000;
            margin: 5px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 11px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 11px;
        }

        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            font-size: 10px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .total-section {
            margin-top: 5px;
            font-size: 11px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .total-big {
            font-size: 13px;
            font-weight: bold;
        }

        .footer-note {
            margin-top: 8px;
            font-size: 9.5px;
            text-align: center;
            line-height: 1.3;
        }

        /* Strict Thermal Print Media Override (Format 80mm, pas de marges, nom du laveur JAMAIS visible) */
        @media print {
            @page {
                size: 80mm auto;
                margin: 0mm !important;
            }

            html, body {
                width: 80mm !important;
                max-width: 80mm !important;
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .toolbar-wrapper, .print\:hidden {
                display: none !important;
            }

            .ticket-wrapper {
                width: 80mm !important;
                max-width: 80mm !important;
                margin: 0 !important;
                padding: 2mm 3mm !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Screen Actions Toolbar (Zero emoji, monochrome) -->
    <div class="toolbar-wrapper">
        <div class="toolbar">
            <a href="{{ route('caisse.dashboard') }}" class="btn btn-back">
                &larr; Caisse
            </a>
            <button onclick="window.print()" class="btn btn-print">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Imprimer Reçu (POS 80mm)</span>
            </button>
            <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-whatsapp" title="Envoyer par WhatsApp au client">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>WhatsApp</span>
            </a>
            <a href="{{ route('caisse.depot') }}" class="btn btn-back">+ Nouveau</a>
        </div>
        <div class="print-tip">
            Impression thermique POS 80mm &bull; Le dévidage s'exécute directement sur rouleau ticket.
        </div>
    </div>

    <!-- 80mm Thermal Ticket Roll -->
    <div class="ticket-wrapper">
        <!-- En-tête de l'Établissement -->
        <div class="center">
            <div class="title bold">{{ strtoupper($facture->pressing->nom) }}</div>
            <div>{{ $facture->pressing->isLavage() ? 'STATION DE LAVAGE AUTO / MOTO' : 'PRESSING & BLANCHISSERIE' }}</div>
            <div>{{ $facture->pressing->quartier }}, {{ $facture->pressing->ville }}</div>
            @if($facture->pressing->telephone)
                <div>Tél : {{ $facture->pressing->telephone }}</div>
            @endif
        </div>

        <!-- Numéro de Ticket -->
        <div class="center ticket-num bold">
            TICKET : {{ $facture->num_ticket }}
        </div>

        <!-- Informations Client & Date -->
        <div class="info-row">
            <span>Date :</span>
            <span class="bold">{{ $facture->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Client :</span>
            <span class="bold">{{ $facture->client_nom ?? ($facture->client?->nom ?? 'Client de passage') }}</span>
        </div>
        <div class="info-row">
            <span>Téléphone :</span>
            <span class="bold">{{ $facture->client_telephone ?? ($facture->client?->telephone ?? 'Non renseigné') }}</span>
        </div>
        @if($facture->vehicule)
        <div class="info-row">
            <span>Véhicule :</span>
            <span class="bold">{{ $facture->vehicule->immatriculation }} ({{ $facture->vehicule->marque_modele }})</span>
        </div>
        @endif
        @if($facture->client_email)
        <div class="info-row">
            <span>Email :</span>
            <span>{{ $facture->client_email }}</span>
        </div>
        @endif
        <div class="info-row">
            <span>Guichetier :</span>
            <span>{{ $facture->user->name }}</span>
        </div>
        <div class="info-row">
            <span>{{ $facture->isLavage() ? 'Restitution prévue :' : 'Retrait prévu :' }}</span>
            <span class="bold">{{ $facture->date_retrait_prevue ? $facture->date_retrait_prevue->format('d/m/Y') : 'À convenir' }}</span>
        </div>

        <!-- IMPORTANT : Conformément aux exigences strictes, le nom du laveur n'apparaît JAMAIS sur ce ticket client -->

        <div class="divider"></div>

        <!-- Détail des Articles / Prestations -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Prestation</th>
                    <th style="width: 15%;" class="text-center">Qté</th>
                    <th style="width: 20%;" class="text-right">P.U</th>
                    <th style="width: 20%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->ligneFactures as $ligne)
                    <tr>
                        <td class="bold">{{ $ligne->service ? $ligne->service->designation : 'Prestation' }}</td>
                        <td class="text-center">{{ $ligne->quantite }}</td>
                        <td class="text-right">{{ number_format((float) $ligne->prix_applique, 0, ',', ' ') }}</td>
                        <td class="text-right bold">{{ number_format((float) ($ligne->quantite * $ligne->prix_applique), 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="double-divider"></div>

        <!-- Totaux & Règle Financière -->
        <div class="total-section">
            @if((float) $facture->remise_fidelite > 0)
                <div class="total-row">
                    <span>Montant Brut :</span>
                    <span>{{ number_format((float) ($facture->montant_brut ?: $facture->montant_total), 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="total-row bold" style="color: #000;">
                    <span>Remise Fidélité :</span>
                    <span>-{{ number_format((float) $facture->remise_fidelite, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($facture->details_remise)
                    <div style="font-size: 8px; text-align: right; color: #555; margin-bottom: 3px;">
                        ({{ $facture->details_remise }})
                    </div>
                @endif
            @else
                <div class="total-row total-big">
                    <span>TOTAL :</span>
                    <span>{{ number_format((float) $facture->montant_total, 0, ',', ' ') }} FCFA</span>
                </div>
            @endif
            <div class="total-row">
                <span>Acompte versé :</span>
                <span>0 FCFA</span>
            </div>
            <div class="total-row bold total-big" style="border-top: 1px dashed #000; padding-top: 3px;">
                <span>NET A PAYER :</span>
                <span>{{ number_format((float) $facture->montant_total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Mentions Obligatoires -->
        <div class="footer-note">
            <div class="bold" style="text-transform: uppercase; margin-bottom: 3px;">
                @if($facture->isLavage())
                    PAIEMENT À 100% LORS DE LA RESTITUTION DU VÉHICULE.
                @else
                    PAIEMENT À 100% LORS DU RETRAIT DE VOS ARTICLES.
                @endif
            </div>
            <div>{{ $facture->isLavage() ? 'Présentez ce ticket lors de la restitution.' : 'Présentez ce ticket lors de la récupération.' }}</div>
            <div style="margin-top: 3px;">Merci pour votre fidélité !</div>
            <div style="font-size: 8px; margin-top: 4px;">ERP SUITE &bull; {{ date('d/m/Y H:i') }}</div>
        </div>
    </div>

    <!-- Script d'impression automatique -->
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
