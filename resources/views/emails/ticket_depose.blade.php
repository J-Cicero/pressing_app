<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $facture->num_ticket }} — {{ $facture->pressing->nom ?? 'ERP' }}</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f4f4f5; font-family: Arial, sans-serif; color: #18181b; font-size: 13px;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 10px; padding: 20px;">
        <div style="border-bottom: 1px solid #e4e4e7; padding-bottom: 10px; margin-bottom: 12px;">
            <strong style="font-size: 15px;">{{ $facture->pressing->nom ?? 'ERP SUITE' }}</strong>
            <span style="float: right; font-family: monospace; font-weight: bold;">{{ $facture->num_ticket }}</span>
        </div>

        <p style="margin: 0 0 12px 0;">
            Bonjour <strong>{{ $facture->client_nom }}</strong>,<br>
            @if($facture->isLavage())
                Votre véhicule @if($facture->vehicule)<strong>({{ $facture->vehicule->immatriculation }})</strong>@endif a bien été pris en charge à notre station.
            @else
                Vos vêtements ont bien été déposés à notre agence.
            @endif
        </p>

        <div style="background: #f4f4f5; border-radius: 8px; padding: 12px; margin-bottom: 12px; font-size: 13px; line-height: 1.6;">
            <strong>Ticket N° :</strong> <span style="font-family: monospace; font-weight: bold;">{{ $facture->num_ticket }}</span><br>
            <strong>Date de retrait prévue :</strong> {{ $facture->date_retrait_prevue ? $facture->date_retrait_prevue->format('d/m/Y') : 'Dès que prêt' }}<br>
            <strong>Montant à régler au retrait :</strong> <span style="font-family: monospace; font-weight: bold;">{{ number_format((float)$facture->montant_total, 0, ',', ' ') }} FCFA</span>
            @if((float) $facture->remise_fidelite > 0)
                <br><span style="font-size: 11px; color: #52525b;">(Remise fidélité appliquée : -{{ number_format((float)$facture->remise_fidelite, 0, ',', ' ') }} FCFA)</span>
            @endif
        </div>

        <p style="margin: 0 0 8px 0; color: #52525b; font-size: 12px;">
            Nous vous enverrons un message dès que tout sera prêt !<br>
            Merci de votre confiance,<br>
            <strong>L'équipe {{ $facture->pressing->nom ?? 'ERP SUITE' }}</strong>
        </p>
    </div>
</body>
</html>
