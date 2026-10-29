<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facture->isLavage() ? 'Prise en charge de votre véhicule' : 'Dépôt de vos vêtements' }} — {{ $facture->pressing->nom ?? 'ERP' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f4f4f5; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 580px; background-color: #18181b; border-radius: 16px; border: 1px solid #27272a; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);">
                    
                    <!-- Header Noir/Gris Monochromatique -->
                    <tr>
                        <td style="background-color: #18181b; padding: 28px 32px; border-bottom: 1px solid #27272a; text-align: left;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 19px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff; text-transform: uppercase;">
                                            {{ $facture->pressing->nom ?? 'ERP SUITE' }}
                                        </div>
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #a1a1aa; margin-top: 4px; font-weight: 600;">
                                            {{ $facture->isLavage() ? 'Station de Lavage Auto & Moto • Soin Automobile' : 'Service de Blanchisserie & Soin du Linge' }}
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

                    <!-- Chapeau d'accueil -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px;">
                            <h1 style="font-size: 22px; font-weight: 700; color: #ffffff; margin: 0 0 16px 0; letter-spacing: -0.5px;">
                                Bonjour {{ $facture->client_nom }},
                            </h1>
                            @if($facture->isLavage())
                                <p style="font-size: 14px; line-height: 1.7; color: #d4d4d8; margin: 0 0 20px 0;">
                                    Nous sommes ravis de vous accueillir dans notre station <strong style="color: #ffffff;">{{ $facture->pressing->nom }}</strong> ! Nous avons bien enregistré la prise en charge de votre véhicule @if($facture->vehicule)<strong style="color: #ffffff;">({{ $facture->vehicule->immatriculation }} — {{ $facture->vehicule->marque_modele }})</strong>@endif, et notre équipe s'en occupe déjà avec le plus grand soin.
                                </p>
                                <p style="font-size: 14px; line-height: 1.7; color: #a1a1aa; margin: 0;">
                                    Voici le récapitulatif détaillé de vos prestations de lavage :
                                </p>
                            @else
                                <p style="font-size: 14px; line-height: 1.7; color: #d4d4d8; margin: 0 0 20px 0;">
                                    Nous sommes ravis de vous accueillir dans notre agence <strong style="color: #ffffff;">{{ $facture->pressing->nom }}</strong> ! Nous avons bien enregistré le dépôt de vos vêtements, et notre équipe s'en occupe déjà avec le plus grand soin.
                                </p>
                                <p style="font-size: 14px; line-height: 1.7; color: #a1a1aa; margin: 0;">
                                    Voici le récapitulatif détaillé de vos effets confiés à notre blanchisserie :
                                </p>
                            @endif
                        </td>
                    </tr>

                    <!-- Grille des détails -->
                    <tr>
                        <td style="padding: 0 32px 24px 32px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; border-radius: 12px; border: 1px solid #27272a; padding: 20px;">
                                <tr>
                                    <td width="50%" style="padding-right: 12px; vertical-align: top;">
                                        <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700;">
                                            {{ $facture->isLavage() ? 'Prise en charge' : 'Date de dépôt' }}
                                        </div>
                                        <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-top: 6px;">
                                            {{ $facture->created_at->format('d/m/Y à H:i') }}
                                        </div>
                                    </td>
                                    <td width="50%" style="padding-left: 12px; border-left: 1px solid #27272a; vertical-align: top;">
                                        <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700;">
                                            {{ $facture->isLavage() ? 'Restitution estimée dès le' : 'Retrait estimé dès le' }}
                                        </div>
                                        <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-top: 6px;">
                                            {{ $facture->date_retrait_prevue ? $facture->date_retrait_prevue->format('d/m/Y') : 'Très prochainement' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Tableau des prestations / articles -->
                    <tr>
                        <td style="padding: 0 32px 24px 32px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #27272a; text-align: left;">
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700;">
                                            {{ $facture->isLavage() ? 'Prestations de Lavage' : 'Articles' }}
                                        </th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: center;">Quantité</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: right;">Prix Unitaire</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: right;">Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facture->ligneFactures as $ligne)
                                        <tr style="border-bottom: 1px solid #27272a;">
                                            <td style="padding: 14px 0; font-size: 13px; color: #f4f4f5; font-weight: 600;">
                                                {{ $ligne->service->designation ?? 'Prestation' }}
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

                    <!-- Encadré Total & Information -->
                    <tr>
                        <td style="padding: 0 32px 32px 32px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; border-radius: 12px; border: 1px solid #27272a; padding: 20px; margin-bottom: 20px;">
                                @if((float) $facture->remise_fidelite > 0)
                                    <tr>
                                        <td style="font-size: 12px; color: #a1a1aa; padding-bottom: 8px;">
                                            Montant Brut
                                        </td>
                                        <td align="right" style="font-size: 13px; color: #a1a1aa; font-family: monospace; padding-bottom: 8px;">
                                            {{ number_format((float)$facture->montant_brut, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; color: #ffffff; font-weight: 700; padding-bottom: 12px;">
                                            {{ $facture->details_remise ?? 'Privilège Fidélité appliqué' }}
                                        </td>
                                        <td align="right" style="font-size: 13px; color: #ffffff; font-weight: 700; font-family: monospace; padding-bottom: 12px;">
                                            -{{ number_format((float)$facture->remise_fidelite, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="font-size: 13px; font-weight: 700; color: #a1a1aa; text-transform: uppercase; letter-spacing: 1px;">
                                        Montant Total à régler
                                    </td>
                                    <td align="right" style="font-size: 22px; font-weight: 900; color: #ffffff; font-family: monospace;">
                                        {{ number_format((float)$facture->montant_total, 2, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            </table>

                            @if($facture->avantageFidelite)
                                <div style="background-color: #18181b; border: 1px solid #ffffff; border-radius: 12px; padding: 16px; margin-bottom: 20px; font-size: 12px; color: #f4f4f5; line-height: 1.6;">
                                    <strong style="color: #ffffff; text-transform: uppercase; letter-spacing: 0.5px;">Suivi de votre Privilège Fidélité :</strong><br>
                                    • Utilisations restantes après ce ticket : <strong>{{ $facture->avantageFidelite->utilisations_restantes }} passage(s)</strong><br>
                                    • Validité du privilège :
                                    @if($facture->avantageFidelite->date_expiration)
                                        <strong>Durée limitée jusqu'au {{ $facture->avantageFidelite->date_expiration->format('d/m/Y') }}</strong>
                                    @else
                                        <strong>Validité permanente (sans date limite)</strong>
                                    @endif
                                </div>
                            @endif

                            <div style="background-color: #18181b; border: 1px solid #3f3f46; border-radius: 12px; padding: 16px; font-size: 13px; color: #d4d4d8; line-height: 1.6;">
                                @if($facture->isLavage())
                                    <strong>Pour votre sérénité :</strong> Aucun règlement n'est exigé à l'avance. Vous réglerez le montant total de {{ number_format((float)$facture->montant_total, 2, ',', ' ') }} FCFA directement en caisse lorsque vous viendrez récupérer votre véhicule propre et impeccable.
                                @else
                                    <strong>Pour votre sérénité :</strong> Aucun règlement n'est demandé au dépôt. Vous réglerez le montant total de {{ number_format((float)$facture->montant_total, 2, ',', ' ') }} FCFA directement au guichet lorsque vous viendrez récupérer vos vêtements impeccables.
                                @endif
                            </div>

                            <p style="font-size: 13px; color: #a1a1aa; line-height: 1.6; margin: 20px 0 0 0;">
                                @if($facture->isLavage())
                                    Dès que le lavage et les finitions seront terminés, nous vous enverrons un nouvel e-mail pour vous prévenir que votre véhicule est prêt !
                                @else
                                    Dès que le lavage et le repassage seront terminés, nous vous enverrons un nouvel e-mail pour vous prévenir que tout est prêt !
                                @endif
                            </p>
                        </td>
                    </tr>

                    <!-- Footer & Signature -->
                    <tr>
                        <td style="background-color: #09090b; padding: 24px 32px; border-top: 1px solid #27272a; text-align: center;">
                            <div style="font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 6px;">
                                L'équipe {{ $facture->pressing->nom }}
                            </div>
                            <div style="font-size: 12px; color: #71717a;">
                                {{ $facture->pressing->ville }} @if($facture->pressing->quartier) • {{ $facture->pressing->quartier }} @endif
                                @if($facture->pressing->telephone) • Tél: {{ $facture->pressing->telephone }} @endif
                            </div>
                            <div style="font-size: 11px; color: #52525b; margin-top: 16px; font-style: italic;">
                                {{ $facture->isLavage() ? 'Merci de confier votre véhicule à notre station. À très bientôt !' : 'Merci de votre confiance ! À très bientôt dans notre agence.' }}
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
