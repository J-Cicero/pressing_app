<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de paiement & {{ $facture->isLavage() ? 'Restitution véhicule' : 'Retrait vêtements' }} — {{ $facture->pressing->nom ?? 'ERP' }}</title>
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
                                            {{ $facture->isLavage() ? 'Reçu de Paiement & Restitution Véhicule' : 'Reçu de Paiement & Retrait Blanchisserie' }}
                                        </div>
                                    </td>
                                    <td align="right">
                                        <span style="display: inline-block; background-color: #ffffff; color: #09090b; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; font-family: monospace;">
                                            PAYÉ • N° {{ $facture->num_ticket }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Hero Banner -->
                    <tr>
                        <td style="background-color: #09090b; border-bottom: 1px solid #27272a; padding: 28px 32px; text-align: center;">
                            <div style="display: inline-block; width: 48px; height: 48px; line-height: 48px; background-color: #ffffff; color: #09090b; font-size: 20px; font-weight: bold; border-radius: 50%; margin-bottom: 12px;">
                                ✓
                            </div>
                            <h2 style="font-size: 20px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0; letter-spacing: -0.5px;">
                                Paiement confirmé & {{ $facture->isLavage() ? 'véhicule restitué' : 'articles retirés' }}
                            </h2>
                            <p style="font-size: 13px; color: #a1a1aa; margin: 0;">
                                Réglé le {{ ($facture->paye_at ?? now())->format('d/m/Y à H:i') }}
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 22px; font-weight: 700; color: #ffffff; margin: 0 0 16px 0; letter-spacing: -0.5px;">
                                Merci pour votre confiance, {{ $facture->client_nom }} !
                            </h1>
                            <p style="font-size: 14px; line-height: 1.7; color: #d4d4d8; margin: 0 0 24px 0;">
                                Nous vous confirmons la bonne réception de votre règlement ainsi que la restitution {{ $facture->isLavage() ? 'de votre véhicule' : 'de vos vêtements' }} pour le ticket <strong style="color: #ffffff; font-family: monospace;">{{ $facture->num_ticket }}</strong> auprès de <strong style="color: #ffffff;">{{ $facture->pressing->nom }}</strong>.
                            </p>

                            <!-- Détail des lignes -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; margin-bottom: 24px;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #27272a; text-align: left;">
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700;">
                                            {{ $facture->isLavage() ? 'Prestations' : 'Articles' }}
                                        </th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: center;">Qté</th>
                                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #71717a; font-weight: 700; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facture->ligneFactures as $ligne)
                                        <tr style="border-bottom: 1px solid #27272a;">
                                            <td style="padding: 12px 0; font-size: 14px; color: #f4f4f5; font-weight: 600;">
                                                {{ $ligne->service->designation ?? 'Prestation' }}
                                            </td>
                                            <td style="padding: 12px 0; font-size: 14px; color: #a1a1aa; text-align: center; font-family: monospace;">
                                                x{{ $ligne->quantite }}
                                            </td>
                                            <td style="padding: 12px 0; font-size: 14px; color: #ffffff; text-align: right; font-weight: 700; font-family: monospace;">
                                                {{ number_format((float)($ligne->quantite * $ligne->prix_applique), 0, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Montant encaissé -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; border-radius: 12px; border: 1px solid #27272a; padding: 20px; margin-bottom: 24px; text-align: center;">
                                <tr>
                                    <td>
                                        @if((float) $facture->remise_fidelite > 0)
                                            <div style="font-size: 12px; color: #a1a1aa; margin-bottom: 6px;">
                                                Montant brut : {{ number_format((float) $facture->montant_brut, 0, ',', ' ') }} FCFA • Remise fidélité : -{{ number_format((float) $facture->remise_fidelite, 0, ',', ' ') }} FCFA
                                            </div>
                                        @endif
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #71717a; font-weight: 700;">
                                            Montant Total Encaissé (100% Réglé)
                                        </div>
                                        <div style="font-size: 28px; font-weight: 900; color: #ffffff; font-family: monospace; margin-top: 8px;">
                                            {{ number_format((float)$facture->montant_total, 0, ',', ' ') }} FCFA
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            @if($regleDebloquee)
                                <!-- Bannière Fidélité Débloquée -->
                                <div style="background-color: #ffffff; color: #09090b; border-radius: 12px; padding: 20px; margin-bottom: 24px; text-align: center;">
                                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
                                        ★ Nouveau Privilège Fidélité Débloqué ★
                                    </div>
                                    <div style="font-size: 16px; font-weight: 900; margin-bottom: 6px;">
                                        {{ $regleDebloquee->description }}
                                    </div>
                                    <div style="font-size: 12px; color: #3f3f46; line-height: 1.6;">
                                        Palier atteint : <strong>{{ $regleDebloquee->seuil_visites }} visites complétées</strong>.<br>
                                        Quota accordé : <strong>{{ $avantageDebloque?->utilisations_restantes ?? ($regleDebloquee->nombre_utilisations ?: 1) }} utilisation(s)</strong><br>
                                        @if($avantageDebloque?->date_expiration)
                                            Durée limitée : <strong>Valable {{ $regleDebloquee->duree_jours }} jour(s) — jusqu'au {{ $avantageDebloque->date_expiration->format('d/m/Y') }}</strong>
                                        @elseif($regleDebloquee->duree_jours)
                                            Durée limitée : <strong>Valable {{ $regleDebloquee->duree_jours }} jour(s)</strong>
                                        @else
                                            Validité : <strong>Sans limite de durée</strong>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($facture->avantageFidelite && $facture->avantageFidelite->utilisations_restantes > 0)
                                <div style="background-color: #18181b; border: 1px solid #3f3f46; border-radius: 12px; padding: 16px; margin-bottom: 20px; font-size: 12px; color: #d4d4d8; line-height: 1.6; text-align: center;">
                                    <strong style="color: #ffffff;">Privilège en cours :</strong> Il vous reste <strong>{{ $facture->avantageFidelite->utilisations_restantes }} utilisation(s)</strong>
                                    @if($facture->avantageFidelite->date_expiration)
                                        valable(s) jusqu'au <strong>{{ $facture->avantageFidelite->date_expiration->format('d/m/Y') }}</strong>.
                                    @else
                                        sur votre avantage fidélité.
                                    @endif
                                </div>
                            @endif

                            <p style="font-size: 13px; color: #a1a1aa; line-height: 1.6; margin: 0; text-align: center;">
                                Ce courriel tient lieu de reçu électronique de paiement pour le ticket <strong style="color: #ffffff; font-family: monospace;">{{ $facture->num_ticket }}</strong>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #09090b; padding: 24px 32px; border-top: 1px solid #27272a; text-align: center;">
                            <div style="font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 6px;">
                                À très bientôt chez {{ $facture->pressing->nom }} !
                            </div>
                            <div style="font-size: 11px; color: #52525b; margin-top: 12px;">
                                ERP Suite • {{ $facture->isLavage() ? 'Solution de Gestion Lavage Automobile & Moto' : 'Solution de Gestion Pressing & Blanchisserie' }}
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
