<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rappel retrait {{ $facture->num_ticket }}</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f4f4f5; font-family: Arial, sans-serif; color: #18181b; font-size: 13px;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 10px; padding: 20px;">
        <div style="border-bottom: 1px solid #e4e4e7; padding-bottom: 10px; margin-bottom: 12px;">
            <strong style="font-size: 15px;">{{ $facture->pressing->nom ?? 'ERP SUITE' }}</strong>
            <span style="float: right; font-family: monospace; font-weight: bold;">{{ $facture->num_ticket }}</span>
        </div>

        <p style="margin: 0 0 12px 0;">
            Bonjour <strong>{{ $facture->client_nom }}</strong>,<br>
            Rappel : la date de retrait de vos vêtements est prévue aujourd'hui.
        </p>

        <div style="background: #f4f4f5; border-radius: 8px; padding: 12px;">
            <strong>Montant à régler :</strong> <span style="font-family: monospace; font-weight: bold;">{{ number_format((float)$facture->montant_total, 0, ',', ' ') }} FCFA</span><br>
            <strong>Agence :</strong> {{ $facture->pressing->nom }} ({{ $facture->pressing->ville }})
        </div>
    </div>
</body>
</html>
