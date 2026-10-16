<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date de retrait de vos vêtements — {{ $facture->pressing->nom ?? 'Pressing' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f4f4f5; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 580px; background-color: #18181b; border-radius: 16px; border: 1px solid #27272a; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #18181b; padding: 28px 32px; border-bottom: 1px solid #27272a; text-align: left;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 19px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff; text-transform: uppercase;">
                                            {{ $facture->pressing->nom ?? 'ERP SUITE' }}
                                        </div>
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #a1a1aa; margin-top: 4px; font-weight: 600;">
                                            Service de Blanchisserie & Soin du Linge
                                        </div>
                                    </td>
                                    <td align="right">
                                        <span style="display: inline-block; background-color: #ffffff; color: #09090b; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; font-family: monospace;">
                                            N° {{ $facture->num_ticket }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Alert Banner -->
                    <tr>
                        <td style="background-color: #09090b; border-bottom: 1px solid #27272a; padding: 24px 32px; text-align: center;">
                            <h2 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0; letter-spacing: -0.5px;">
                                La date de retrait de vos vêtements est arrivée aujourd'hui
                            </h2>
                            <p style="font-size: 13px; color: #a1a1aa; margin: 0;">
                                Vos effets confiés sont traités et vous attendent au guichet de notre agence.
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 20px; font-weight: 700; color: #ffffff; margin: 0 0 16px 0; letter-spacing: -0.5px;">
                                Bonjour {{ $facture->client_nom }},
                            </h1>
                            <p style="font-size: 14px; line-height: 1.7; color: #d4d4d8; margin: 0 0 24px 0;">
                                Nous vous informons que la date convenue pour la récupération de vos vêtements (ticket <strong style="color: #ffffff; font-family: monospace;">{{ $facture->num_ticket }}</strong> déposé le {{ $facture->created_at->format('d/m/Y') }}) est échue ce jour. Vous pouvez passer à notre guichet dès aujourd'hui pour retirer vos articles.
                            </p>

                            <!-- Prestations List -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; margin-bottom: 24px;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #27272a; text-align: left;">
                                        <th style="padding: 8px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700;">Articles</th>
                                        <th style="padding: 8px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: center;">Qté</th>
                                        <th style="padding: 8px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facture->ligneFactures as $ligne)
                                        <tr style="border-bottom: 1px solid #27272a;">
                                            <td style="padding: 10px 0; font-size: 13px; color: #f4f4f5; font-weight: 600;">
                                                {{ $ligne->service->designation ?? 'Prestation' }}
                                            </td>
                                            <td style="padding: 10px 0; font-size: 13px; color: #a1a1aa; text-align: center; font-family: monospace;">
                                                x{{ $ligne->quantite }}
                                            </td>
                                            <td style="padding: 10px 0; font-size: 13px; color: #ffffff; text-align: right; font-weight: 700; font-family: monospace;">
                                                {{ number_format((float)($ligne->quantite * $ligne->prix_applique), 0, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Total -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; border-radius: 12px; border: 1px solid #27272a; padding: 20px; margin-bottom: 24px; text-align: center;">
                                <tr>
                                    <td>
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #71717a; font-weight: 700;">
                                            Montant 100% exigible au retrait
                                        </div>
                                        <div style="font-size: 26px; font-weight: 900; color: #ffffff; font-family: monospace; margin-top: 6px;">
                                            {{ number_format((float)$facture->montant_total, 0, ',', ' ') }} FCFA
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Localisation -->
                            <div style="background-color: #18181b; border: 1px solid #27272a; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                                <div style="font-size: 12px; font-weight: 800; color: #ffffff; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Retrait au guichet :
                                </div>
                                <div style="font-size: 13px; color: #d4d4d8; line-height: 1.6;">
                                    <strong style="color: #ffffff;">Agence :</strong> {{ $facture->pressing->nom }}<br>
                                    <strong style="color: #ffffff;">Adresse :</strong> {{ $facture->pressing->quartier }}, {{ $facture->pressing->ville }}<br>
                                    @if($facture->pressing->telephone)
                                        <strong style="color: #ffffff;">Téléphone :</strong> {{ $facture->pressing->telephone }}
                                    @endif
                                </div>
                            </div>

                            <p style="font-size: 12px; color: #a1a1aa; line-height: 1.6; margin: 0;">
                                Présentez simplement votre numéro de ticket <strong style="color: #ffffff; font-family: monospace;">{{ $facture->num_ticket }}</strong> ou votre nom au guichetier.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #09090b; padding: 20px 32px; border-top: 1px solid #27272a; text-align: center;">
                            <div style="font-size: 13px; font-weight: 800; color: #ffffff;">
                                L'équipe {{ $facture->pressing->nom }}
                            </div>
                            <div style="font-size: 10px; color: #52525b; margin-top: 6px;">
                                ERP Suite • Service de Blanchisserie & Pressing Professionnel
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
