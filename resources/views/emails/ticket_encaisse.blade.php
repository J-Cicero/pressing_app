<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu {{ $facture->num_ticket }}</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f4f4f5; font-family: Arial, sans-serif; color: #18181b; font-size: 13px;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 10px; padding: 20px;">
        <div style="border-bottom: 1px solid #e4e4e7; padding-bottom: 10px; margin-bottom: 12px;">
            <strong style="font-size: 15px;">{{ $facture->pressing->nom ?? 'ERP SUITE' }}</strong>
            <span style="float: right; font-family: monospace; font-weight: bold;">PAYÉ • {{ $facture->num_ticket }}</span>
        </div>

        <p style="margin: 0 0 12px 0;">
            Bonjour <strong>{{ $facture->client_nom }}</strong>,<br>
            Paiement reçu et {{ $facture->isLavage() ? 'véhicule restitué' : 'articles retirés' }}.
        </p>

        <div style="background: #f4f4f5; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
            <strong>Total réglé :</strong> <span style="font-family: monospace; font-weight: bold;">{{ number_format((float)$facture->montant_total, 0, ',', ' ') }} FCFA</span>
            @if((float) $facture->remise_fidelite > 0)
                <br><span style="font-size: 12px; color: #52525b;">Remise fidélité appliquée : -{{ number_format((float)$facture->remise_fidelite, 0, ',', ' ') }} FCFA</span>
            @endif
        </div>

        @if($regleDebloquee)
            <div style="border: 1px solid #18181b; border-radius: 8px; padding: 10px; font-size: 12px; margin-bottom: 8px;">
                <strong>Privilège fidélité débloqué :</strong> {{ $regleDebloquee->description }}<br>
                Quota : {{ $avantageDebloque?->utilisations_restantes ?? ($regleDebloquee->nombre_utilisations ?: 1) }} utilisation(s)
                @if($avantageDebloque?->date_expiration)
                    • Valable jusqu'au {{ $avantageDebloque->date_expiration->format('d/m/Y') }} ({{ $regleDebloquee->duree_jours }}j)
                @endif
            </div>
        @endif

        @if($facture->avantageFidelite && $facture->avantageFidelite->utilisations_restantes > 0)
            <div style="font-size: 12px; color: #52525b;">
                <strong>Fidélité restante :</strong> {{ $facture->avantageFidelite->utilisations_restantes }} utilisation(s)
                @if($facture->avantageFidelite->date_expiration)
                    jusqu'au {{ $facture->avantageFidelite->date_expiration->format('d/m/Y') }}
                @endif
            </div>
        @endif
    </div>
</body>
</html>
