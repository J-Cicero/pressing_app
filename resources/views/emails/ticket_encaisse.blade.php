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
            Votre règlement a bien été enregistré et vos {{ $facture->isLavage() ? 'clefs et véhicule vous ont été restitués' : 'vêtements ont bien été retirés' }}.
        </p>

        <div style="background: #f4f4f5; border-radius: 8px; padding: 12px; margin-bottom: 12px; font-size: 13px; line-height: 1.6;">
            <strong>Ticket N° :</strong> <span style="font-family: monospace; font-weight: bold;">{{ $facture->num_ticket }}</span><br>
            <strong>Montant réglé :</strong> <span style="font-family: monospace; font-weight: bold;">{{ number_format((float)$facture->montant_total, 0, ',', ' ') }} FCFA</span>
            @if((float) $facture->remise_fidelite > 0)
                <br><span style="font-size: 11px; color: #52525b;">(Remise fidélité accordée : -{{ number_format((float)$facture->remise_fidelite, 0, ',', ' ') }} FCFA)</span>
            @endif
        </div>

        @if($regleDebloquee)
            <div style="border: 1px solid #18181b; border-radius: 8px; padding: 10px; font-size: 12px; margin-bottom: 12px; line-height: 1.5;">
                🎁 <strong>Privilège fidélité débloqué :</strong> {{ $regleDebloquee->description }}<br>
                Valable sur vos prochains passages !
            </div>
        @endif

        <p style="margin: 0 0 8px 0; color: #52525b; font-size: 12px;">
            Merci pour votre confiance et à très bientôt,<br>
            <strong>L'équipe {{ $facture->pressing->nom ?? 'ERP SUITE' }}</strong>
        </p>
    </div>
</body>
</html>
