<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Thermal - {{ $facture->num_ticket }}</title>
    <style>
        /* Base Thermal 80mm Settings */
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
            color: #000;
            background: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Screen Toolbar */
        .toolbar {
            max-width: 80mm;
            margin: 20px auto 12px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
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
            box-shadow: 0 1px 3px rgba(79, 70, 229, 0.3);
        }

        .btn-print:hover {
            background: #4338ca;
        }

        .btn-back {
            background: #ffffff;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .btn-back:hover {
            background: #f1f5f9;
        }

        /* 80mm Ticket Container */
        .ticket-wrapper {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 5mm 4mm;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .title {
            font-size: 15px;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .ticket-num {
            font-size: 16px;
            margin: 6px 0;
            padding: 4px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .double-divider {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
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
            padding: 4px 0;
            vertical-align: top;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .total-section {
            margin-top: 6px;
            font-size: 12px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .total-big {
            font-size: 14px;
            font-weight: bold;
        }

        .footer-note {
            margin-top: 10px;
            font-size: 10px;
            text-align: center;
            line-height: 1.3;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 80mm !important;
            }

            .toolbar, header, footer, aside, nav, .print\:hidden {
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
    <!-- Screen Actions (Hidden on Thermal Print) -->
    <div class="toolbar">
        <a href="{{ route('caisse.dashboard') }}" class="btn btn-back">&larr; Caisse</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Imprimer Reçu (80mm)</button>
        <a href="{{ route('caisse.depot') }}" class="btn btn-back">+ Nouveau</a>
    </div>

    <!-- 80mm Ticket Paper -->
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
            <div class="bold" style="text-transform: uppercase; margin-bottom: 4px;">
                PAIEMENT À 100% LORS DU RETRAIT DE VOS ARTICLES.
            </div>
            <div>Gardez précieusement ce ticket pour le retrait.</div>
            <div style="margin-top: 4px;">Merci pour votre fidélité !</div>
            <div style="font-size: 8px; margin-top: 6px;">PRESSINGAPP &bull; {{ date('d/m/Y H:i') }}</div>
        </div>
    </div>

    <!-- Script d'impression automatique -->
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
