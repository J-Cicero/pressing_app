<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez {{ $facture->pressing->nom ?? 'PressingApp' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f4f4f5; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 580px; background-color: #18181b; border-radius: 16px; border: 1px solid #27272a; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);">
                    
                    <!-- Header Noir/Gris Monochromatique -->
                    <tr>
                        <td style="background-color: #18181b; padding: 28px 32px; border-b: 1px solid #27272a; text-align: left;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 19px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff; text-transform: uppercase;">
                                            {{ $facture->pressing->nom ?? 'PRESSING APP' }}
                                        </div>
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #a1a1aa; margin-top: 4px; font-weight: 600;">
                                            Service de Blanchisserie & Soin du Linge
                                        </div>
                                    </td>
                                    <td align="right">
                                        <span style="display: inline-block; background-color: #27272a; border: 1px solid #3f3f46; color: #ffffff; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; font-family: monospace;">
                                            N° {{ $facture->num_ticket }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Chapeau d'accueil chaleureux & humain -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px;">
                            <h1 style="font-size: 22px; font-weight: 700; color: #ffffff; margin: 0 0 16px 0; tracking-tight;">
                                Bonjour {{ $facture->client_nom }}, 👋
                            </h1>
                            <p style="font-size: 14px; line-height: 1.7; color: #d4d4d8; margin: 0 0 20px 0;">
                                Nous sommes ravis de vous accueillir dans notre agence <strong style="color: #ffffff;">{{ $facture->pressing->nom }}</strong> ! Nous avons bien enregistré le dépôt de vos vêtements, et notre équipe s'en occupe déjà avec le plus grand soin.
                            </p>
                            <p style="font-size: 14px; line-height: 1.7; color: #a1a1aa; margin: 0;">
                                Voici le récapitulatif détaillé de vos effets confiés à notre blanchisserie :
                            </p>
                        </td>
                    </tr>

                    <!-- Grille des détails du dépôt -->
                    <tr>
                        <td style="padding: 0 32px 24px 32px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; border-radius: 12px; border: 1px solid #27272a; padding: 20px;">
                                <tr>
                                    <td width="50%" style="padding-right: 12px; vertical-align: top;">
                                        <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700;">
                                            Date de dépôt
                                        </div>
                                        <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-top: 6px;">
                                            {{ $facture->created_at->format('d/m/Y à H:i') }}
                                        </div>
                                    </td>
                                    <td width="50%" style="padding-left: 12px; border-left: 1px solid #27272a; vertical-align: top;">
                                        <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700;">
                                            Retrait estimé dès le
                                        </div>
                                        <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-top: 6px;">
                                            {{ $facture->date_retrait_prevue ? $facture->date_retrait_prevue->format('d/m/Y') : 'Très prochainement' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Tableau des articles déposés -->
                    <tr>
                        <td style="padding: 0 32px 24px 32px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #27272a; text-align: left;">
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700;">Articles</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: center;">Quantité</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: right;">Prix Unitaire</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: right;">Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facture->ligneFactures as $ligne)
                                        <tr style="border-bottom: 1px solid #27272a;">
                                            <td style="padding: 14px 0; font-size: 13px; color: #f4f4f5; font-weight: 600;">
                                                {{ $ligne->service->designation ?? 'Article' }}
                                            </td>
                                            <td style="padding: 14px 0; font-size: 13px; color: #a1a1aa; text-align: center; font-family: monospace;">
                                                x{{ $ligne->quantite }}
                                            </td>
                                            <td style="padding: 14px 0; font-size: 13px; color: #a1a1aa; text-align: right; font-family: monospace;">
                                                {{ number_format((float)$ligne->prix_applique, 2, ',', ' ') }} FCFA
                                            </td>
                                            <td style="padding: 14px 0; font-size: 13px; color: #ffffff; text-align: right; font-weight: 700; font-family: monospace;">
                                                {{ number_format((float)($ligne->quantite * $ligne->prix_applique), 2, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- Encadré Total & Note Humaine -->
                    <tr>
                        <td style="padding: 0 32px 32px 32px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; border-radius: 12px; border: 1px solid #27272a; padding: 20px; margin-bottom: 20px;">
                                <tr>
                                    <td style="font-size: 13px; font-weight: 700; color: #a1a1aa; text-transform: uppercase; letter-spacing: 1px;">
                                        Montant Total à régler
                                    </td>
                                    <td align="right" style="font-size: 22px; font-weight: 900; color: #ffffff; font-family: monospace;">
                                        {{ number_format((float)$facture->montant_total, 2, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            </table>

                            <div style="background-color: #18181b; border: 1px solid #3f3f46; border-radius: 12px; padding: 16px; font-size: 13px; color: #d4d4d8; line-height: 1.6;">
                                💡 <strong>Pour votre sérénité :</strong> Aucun règlement n'est demandé au dépôt. Vous réglerez le montant total de {{ number_format((float)$facture->montant_total, 2, ',', ' ') }} FCFA directement au guichet lorsque vous viendrez récupérer vos vêtements impeccables.
                            </div>

                            <p style="font-size: 13px; color: #a1a1aa; line-height: 1.6; margin: 20px 0 0 0;">
                                Dès que le lavage et le repassage seront terminés, nous vous enverrons un nouvel e-mail pour vous prévenir que tout est prêt !
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Chaleureux & Signature -->
                    <tr>
                        <td style="background-color: #09090b; padding: 24px 32px; border-top: 1px solid #27272a; text-align: center;">
                            <div style="font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 6px;">
                                L'équipe {{ $facture->pressing->nom }}
                            </div>
                            <div style="font-size: 12px; color: #71717a;">
                                {{ $facture->pressing->ville }} @if($facture->pressing->quartier) &bull; {{ $facture->pressing->quartier }} @endif
                                @if($facture->pressing->telephone) &bull; Tél: {{ $facture->pressing->telephone }} @endif
                            </div>
                            <div style="font-size: 11px; color: #52525b; margin-top: 16px; font-style: italic;">
                                Merci de votre confiance ! À très bientôt dans notre agence.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
