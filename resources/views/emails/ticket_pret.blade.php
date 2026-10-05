<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos vêtements sont PRÊTS !</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0f172a; padding: 24px 32px; text-align: left;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 18px; font-weight: 800; letter-spacing: -0.5px; color: #ffffff;">
                                            PRESSING<span style="color: #818cf8; font-weight: 300;">APP</span>
                                        </div>
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 2px;">
                                            {{ $facture->pressing->nom ?? 'Agence Pressing' }}
                                        </div>
                                    </td>
                                    <td align="right">
                                        <span style="display: inline-block; background-color: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #6ee7b7; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; font-family: monospace;">
                                            {{ $facture->num_ticket }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Status Hero Banner -->
                    <tr>
                        <td style="background-color: #ecfdf5; border-bottom: 1px solid #a7f3d0; padding: 20px 32px; text-align: center;">
                            <div style="display: inline-block; width: 44 h-11; line-height: 44px; background-color: #10b981; color: #ffffff; font-size: 20px; font-weight: bold; border-radius: 50%; margin-bottom: 8px;">
                                ✓
                            </div>
                            <h2 style="font-size: 18px; font-weight: 800; color: #065f46; margin: 0 0 4px 0;">
                                Vos vêtements sont PRÊTS pour le retrait !
                            </h2>
                            <p style="font-size: 13px; color: #047857; margin: 0;">
                                Nettoyage & repassage effectués avec soin.
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0;">
                                Bonjour {{ $facture->client_nom }},
                            </h1>
                            <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 24px 0;">
                                Nous avons le plaisir de vous informer que vos articles déposés sous le ticket <strong style="color: #0f172a; font-family: monospace;">{{ $facture->num_ticket }}</strong> sont entièrement prêts et vous attendent à notre guichet.
                            </p>

                            <!-- Total to Pay Box -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 24px; text-align: center;">
                                <tr>
                                    <td>
                                        <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700;">
                                            Montant à régler au guichet
                                        </div>
                                        <div style="font-size: 26px; font-weight: 900; color: #0f172a; font-family: monospace; margin-top: 6px;">
                                            {{ number_format((float)$facture->montant_total, 2, ',', ' ') }} FCFA
                                        </div>
                                        <div style="font-size: 11px; color: #10b981; font-weight: 600; margin-top: 6px;">
                                            Paiement intégral lors du retrait de vos articles
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Pickup Instructions -->
                            <div style="background-color: #f1f5f9; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                                <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">
                                    📍 Où récupérer vos vêtements ?
                                </div>
                                <div style="font-size: 13px; color: #334155; line-height: 1.5;">
                                    <strong>Agence :</strong> {{ $facture->pressing->nom }}<br>
                                    <strong>Adresse :</strong> {{ $facture->pressing->ville }} @if($facture->pressing->quartier) — {{ $facture->pressing->quartier }} @endif<br>
                                    @if($facture->pressing->telephone)
                                        <strong>Téléphone :</strong> {{ $facture->pressing->telephone }}
                                    @endif
                                </div>
                            </div>

                            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin: 0;">
                                Munissez-vous simplement de votre numéro de ticket <strong style="color: #0f172a; font-family: monospace;">{{ $facture->num_ticket }}</strong> ou de votre numéro de téléphone au guichet.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <div style="font-size: 12px; font-weight: 700; color: #334155;">
                                Merci d'avoir choisi {{ $facture->pressing->nom }} !
                            </div>
                            <div style="font-size: 10px; color: #94a3b8; margin-top: 12px;">
                                Ceci est un message automatique envoyé par le système PressingApp. Merci de ne pas y répondre directement.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
