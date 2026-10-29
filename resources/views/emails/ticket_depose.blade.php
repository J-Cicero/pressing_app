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
                Confirmation de la prise en charge de votre véhicule @if($facture->vehicule)<strong>({{ $facture->vehicule->immatriculation }})</strong>@endif.
            @else
                Confirmation du dépôt de vos vêtements.
            @endif
        </p>

        <table width="100%" cellspacing="0" cellpadding="4" style="border-collapse: collapse; margin-bottom: 12px; font-size: 12px;">
            @foreach($facture->ligneFactures as $ligne)
                <tr style="border-bottom: 1px solid #f4f4f5;">
                    <td>{{ $ligne->service->designation ?? 'Prestation' }} (x{{ $ligne->quantite }})</td>
                    <td align="right" style="font-family: monospace;">{{ number_format((float)($ligne->quantite * $ligne->prix_applique), 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
            @if((float) $facture->remise_fidelite > 0)
                <tr>
                    <td>Remise fidélité</td>
                    <td align="right" style="font-family: monospace;">-{{ number_format((float)$facture->remise_fidelite, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endif
            <tr>
                <td><strong>Net à payer au retrait</strong></td>
                <td align="right" style="font-family: monospace; font-weight: bold;">{{ number_format((float)$facture->montant_total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>

        <div style="font-size: 12px; color: #52525b;">
            <strong>Retrait prévu :</strong> {{ $facture->date_retrait_prevue ? $facture->date_retrait_prevue->format('d/m/Y') : 'Dès que prêt' }}
            @if($facture->avantageFidelite)
                <br><strong>Fidélité :</strong> {{ $facture->avantageFidelite->utilisations_restantes }} utilisation(s) restante(s)
                @if($facture->avantageFidelite->date_expiration)
                    (jusqu'au {{ $facture->avantageFidelite->date_expiration->format('d/m/Y') }})
                @endif
            @endif
        </div>
    </div>
</body>
</html>
