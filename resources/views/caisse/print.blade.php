<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $facture->num_ticket }}</title>
    <style>
        /* Modern CSS Reset for 80mm POS Thermal Roll Printers */
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
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Screen Toolbar (Screen only) */
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
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            background: #4f46e5;
            color: #ffffff;
            flex: 1;
        }

        .btn-print:hover {
            background: #4338ca;
        }

        .btn-back {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-back:hover {
            background: #e2e8f0;
        }

        .print-tip {
            margin-top: 8px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
            line-height: 1.3;
        }

        /* 80mm Thermal Receipt Simulation Container */
        .ticket-wrapper {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto 24px;
            background: #ffffff;
            padding: 4mm 3mm;
            border: 1px solid #cbd5e1;
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

        /* Strict Thermal Print Media Override */
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
    <!-- Screen Actions & POS Printer Guidance -->
    <div class="toolbar-wrapper">
        <div class="toolbar">
            <a href="{{ route('caisse.dashboard') }}" class="btn btn-back">&larr; Caisse</a>
            <button onclick="window.print()" class="btn btn-print">🖨️ Imprimer Reçu (POS 80mm)</button>
            <a href="{{ route('caisse.depot') }}" class="btn btn-back">+ Nouveau</a>
        </div>
        <div class="print-tip">
            💡 <strong>Note POS Thermal :</strong> Dans la fenêtre d'impression, sélectionnez votre imprimante thermique 80mm (ou format rouleau Ticket) pour un dévidage direct sans marges A4.
        </div>
    </div>

    <!-- 80mm Thermal Ticket Roll -->
    <div class="ticket-wrapper">
        <!-- En-tête de l'Agence -->
        <div class="center">
            <div class="title bold">{{ strtoupper($facture->pressing->nom) }}</div>
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
            <span>Dépôt le :</span>
            <span class="bold">{{ $facture->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Client :</span>
            <span class="bold">{{ $facture->client_nom ?? 'Client de passage' }}</span>
        </div>
        <div class="info-row">
            <span>Téléphone :</span>
            <span class="bold">{{ $facture->client_telephone ?? 'Non renseigné' }}</span>
        </div>
        @if($facture->client_email)
        <div class="info-row">
            <span>Email :</span>
            <span class="bold">{{ $facture->client_email }}</span>
        </div>
        @endif
        <div class="info-row">
            <span>Caissier :</span>
            <span>{{ $facture->user->name }}</span>
        </div>
        <div class="info-row">
            <span>Retrait prévu :</span>
            <span class="bold">{{ $facture->date_retrait_prevue ? $facture->date_retrait_prevue->format('d/m/Y') : 'À convenir' }}</span>
        </div>

        <div class="divider"></div>

        <!-- Détail des Articles / Prestations -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Article</th>
                    <th style="width: 15%;" class="text-center">Qté</th>
                    <th style="width: 20%;" class="text-right">P.U</th>
                    <th style="width: 20%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->ligneFactures as $ligne)
                    <tr>
                        <td class="bold">{{ $ligne->service ? $ligne->service->designation : 'Article' }}</td>
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
            <div class="total-row total-big">
                <span>TOTAL :</span>
                <span>{{ number_format((float) $facture->montant_total, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="total-row">
                <span>Acompte versé :</span>
                <span>0 FCFA</span>
            </div>
            <div class="total-row bold" style="border-top: 1px dashed #000; padding-top: 3px;">
                <span>NET A PAYER :</span>
                <span>{{ number_format((float) $facture->montant_total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Mentions Obligatoires -->
        <div class="footer-note">
            <div class="bold" style="text-transform: uppercase; margin-bottom: 3px;">
                PAIEMENT À 100% LORS DU RETRAIT DE VOS ARTICLES.
            </div>
            <div>Gardez précieusement ce ticket pour le retrait.</div>
            <div style="margin-top: 3px;">Merci pour votre fidélité !</div>
            <div style="font-size: 8px; margin-top: 4px;">PRESSINGAPP &bull; {{ date('d/m/Y H:i') }}</div>
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
