<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos vêtements sont prêts — {{ $facture->pressing->nom ?? 'PressingApp' }}</title>
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
                                            {{ $facture->pressing->nom ?? 'PRESSING APP' }}
                                        </div>
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #a1a1aa; margin-top: 4px; font-weight: 600;">
                                            Vos vêtements sont prêts
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

                    <!-- Hero Notification Banner -->
                    <tr>
                        <td style="background-color: #09090b; border-bottom: 1px solid #27272a; padding: 28px 32px; text-align: center;">
                            <div style="display: inline-block; width: 48px; height: 48px; line-height: 48px; background-color: #ffffff; color: #09090b; font-size: 22px; font-weight: bold; border-radius: 50%; margin-bottom: 12px;">
                                ✓
                            </div>
                            <h2 style="font-size: 20px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0; tracking-tight;">
                                Vos vêtements sont prêts à être retirés !
                            </h2>
                            <p style="font-size: 13px; color: #a1a1aa; margin: 0;">
                                Nettoyage & repassage soigneusement terminés.
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 22px; font-weight: 700; color: #ffffff; margin: 0 0 16px 0;">
                                Excellent nouvelle, {{ $facture->client_nom }} ! ✨
                            </h1>
                            <p style="font-size: 14px; line-height: 1.7; color: #d4d4d8; margin: 0 0 24px 0;">
                                Les équipes de notre agence <strong style="color: #ffffff;">{{ $facture->pressing->nom }}</strong> ont terminé le soin complet de votre linge (ticket <strong style="color: #ffffff; font-family: monospace;">{{ $facture->num_ticket }}</strong>). Vos affaires sont propres, repassées et n'attendent plus que vous !
                            </p>

                            <!-- Total Encadré -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; border-radius: 12px; border: 1px solid #27272a; padding: 20px; margin-bottom: 24px; text-align: center;">
                                <tr>
                                    <td>
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #71717a; font-weight: 700;">
                                            Montant à régler au guichet
                                        </div>
                                        <div style="font-size: 28px; font-weight: 900; color: #ffffff; font-family: monospace; margin-top: 8px;">
                                            {{ number_format((float)$facture->montant_total, 2, ',', ' ') }} FCFA
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Encadré Récupération -->
                            <div style="background-color: #18181b; border: 1px solid #27272a; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                <div style="font-size: 13px; font-weight: 800; color: #ffffff; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    📍 Point de retrait :
                                </div>
                                <div style="font-size: 13px; color: #d4d4d8; line-height: 1.6;">
                                    <strong style="color: #ffffff;">Agence :</strong> {{ $facture->pressing->nom }}<br>
                                    <strong style="color: #ffffff;">Localisation :</strong> {{ $facture->pressing->ville }} @if($facture->pressing->quartier) — {{ $facture->pressing->quartier }} @endif<br>
                                    @if($facture->pressing->telephone)
                                        <strong style="color: #ffffff;">Contact :</strong> {{ $facture->pressing->telephone }}
                                    @endif
                                </div>
                            </div>

                            <p style="font-size: 13px; color: #a1a1aa; line-height: 1.6; margin: 0;">
                                Présentez simplement votre numéro de ticket <strong style="color: #ffffff; font-family: monospace;">{{ $facture->num_ticket }}</strong> ou votre nom au caissier lors de votre venue.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #09090b; padding: 24px 32px; border-top: 1px solid #27272a; text-align: center;">
                            <div style="font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 6px;">
                                Toute l'équipe {{ $facture->pressing->nom }} vous remercie !
                            </div>
                            <div style="font-size: 11px; color: #52525b; margin-top: 12px;">
                                PressingApp &bull; Solution de Blanchisserie & Gestion Multi-Agences
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
