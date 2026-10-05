<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Dépôt</title>
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
                                        <span style="display: inline-block; background-color: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: #a5b4fc; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; font-family: monospace;">
                                            {{ $facture->num_ticket }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0;">
                                Bonjour {{ $facture->client_nom }},
                            </h1>
                            <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 24px 0;">
                                Nous vous confirmons la bonne réception de vos articles dans notre agence <strong style="color: #0f172a;">{{ $facture->pressing->nom }}</strong>. Voici le récapitulatif officiel de votre dépôt :
                            </p>

                            <!-- Information Cards Grid -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 24px; background-color: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9; padding: 16px;">
                                <tr>
                                    <td width="50%" style="padding-right: 8px;">
                                        <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">Date de dépôt</div>
                                        <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-top: 4px;">
                                            {{ $facture->created_at->format('d/m/Y à H:i') }}
                                        </div>
                                    </td>
                                    <td width="50%" style="padding-left: 8px;">
                                        <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">Date de retrait prévue</div>
                                        <div style="font-size: 13px; font-weight: 700; color: #4f46e5; margin-top: 4px;">
                                            {{ $facture->date_retrait_prevue ? $facture->date_retrait_prevue->format('d/m/Y') : 'À déterminer' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Articles Table -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; margin-bottom: 24px;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700;">Service / Article</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; text-align: center;">Qté</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; text-align: right;">Prix Unitaire</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facture->ligneFactures as $ligne)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 12px 0; font-size: 13px; color: #1e293b; font-weight: 600;">
                                                {{ $ligne->service->designation ?? 'Prestation' }}
                                            </td>
                                            <td style="padding: 12px 0; font-size: 13px; color: #475569; text-align: center; font-family: monospace;">
                                                {{ $ligne->quantite }}
                                            </td>
                                            <td style="padding: 12px 0; font-size: 13px; color: #475569; text-align: right; font-family: monospace;">
                                                {{ number_format((float)$ligne->prix_applique, 2, ',', ' ') }} FCFA
                                            </td>
                                            <td style="padding: 12px 0; font-size: 13px; color: #0f172a; text-align: right; font-weight: 700; font-family: monospace;">
                                                {{ number_format((float)($ligne->quantite * $ligne->prix_applique), 2, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Total & Payment Notice -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="font-size: 14px; font-weight: 700; color: #0f172a;">
                                        Montant Total du Dépôt
                                    </td>
                                    <td align="right" style="font-size: 18px; font-weight: 800; color: #4f46e5; font-family: monospace;">
                                        {{ number_format((float)$facture->montant_total, 2, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            </table>

                            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 16px; border-radius: 0 6px 6px 0; font-size: 12px; color: #1e40af; line-height: 1.5; margin-bottom: 24px;">
                                ℹ️ <strong>Information paiement :</strong> Le règlement s'effectue à 100% lors du retrait de vos vêtements au guichet de votre agence.
                            </div>

                            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin: 0;">
                                Un e-mail de notification vous sera envoyé dès que vos articles seront nettoyés, repassés et prêts à être récupérés.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <div style="font-size: 12px; font-weight: 700; color: #334155;">
                                {{ $facture->pressing->nom }}
                            </div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                {{ $facture->pressing->ville }} @if($facture->pressing->quartier) &bull; {{ $facture->pressing->quartier }} @endif
                                @if($facture->pressing->telephone) &bull; Tél : {{ $facture->pressing->telephone }} @endif
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
