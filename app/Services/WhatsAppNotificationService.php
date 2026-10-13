<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Facture;
use App\Models\RegleFidelite;
use App\Models\Vehicule;

class WhatsAppNotificationService
{
    /**
     * Clean and format phone number for international WhatsApp link (e.g. 228XXXXXXXX).
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Remove spaces, dots, dashes, parentheses and plus
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // If leading double zero (00228...), strip 00
        if (str_starts_with($cleaned, '00')) {
            $cleaned = substr($cleaned, 2);
        }

        // Standard Togolese local numbers are 8 digits (e.g., 90123456)
        if (strlen($cleaned) === 8) {
            $cleaned = '228'.$cleaned;
        }

        return $cleaned;
    }

    /**
     * Build the https://wa.me/<number>?text=<encoded_message> URL.
     */
    public function buildUrl(string $phone, string $message): string
    {
        $formattedPhone = $this->formatPhoneNumber($phone);

        return 'https://wa.me/'.$formattedPhone.'?text='.rawurlencode($message);
    }

    /**
     * 1. Notification for newly recorded deposit / wash ticket.
     */
    public function urlDepot(Facture $facture): string
    {
        $clientNom = $facture->client_nom ?? ($facture->client?->nom ?? 'Cher(e) Client(e)');
        $phone = $facture->client_telephone ?? ($facture->client?->telephone ?? '');
        $pressingNom = $facture->pressing?->nom ?? 'Notre Agence';
        $dateRetrait = $facture->date_retrait_prevue ? $facture->date_retrait_prevue->format('d/m/Y') : 'À convenir';
        $montant = number_format((float) $facture->montant_total, 0, ',', ' ');
        $activiteType = $facture->pressing?->isLavage() ? 'votre lavage de véhicule' : 'vos vêtements';

        $articles = $facture->ligneFactures->map(function ($ligne) {
            $nom = $ligne->service ? $ligne->service->designation : 'Article';

            return "- {$nom} (x{$ligne->quantite})";
        })->implode("\n");

        $message = "Bonjour {$clientNom},\n\n"
            ."Votre bon de dépôt a été enregistré avec succès chez {$pressingNom}.\n"
            ."Ticket N° : {$facture->num_ticket}\n"
            ."Prestations :\n{$articles}\n"
            ."Montant à régler au retrait : {$montant} FCFA\n"
            ."Date prévue : {$dateRetrait}\n\n"
            ."Rappel : 100% du paiement est exigible lors de la restitution de {$activiteType}.\n"
            .'Merci pour votre confiance !';

        return $this->buildUrl($phone, $message);
    }

    /**
     * 2. Notification when items or vehicle are ready for collection.
     */
    public function urlPret(Facture $facture): string
    {
        $clientNom = $facture->client_nom ?? ($facture->client?->nom ?? 'Cher(e) Client(e)');
        $phone = $facture->client_telephone ?? ($facture->client?->telephone ?? '');
        $pressingNom = $facture->pressing?->nom ?? 'Notre Agence';
        $montant = number_format((float) $facture->montant_total, 0, ',', ' ');
        $activiteType = $facture->pressing?->isLavage() ? 'Votre véhicule est propre et prêt' : 'Vos vêtements sont prêts';

        $message = "Bonjour {$clientNom},\n\n"
            ."Bonne nouvelle ! {$activiteType} pour le retrait chez {$pressingNom} (Ticket : {$facture->num_ticket}).\n"
            ."Net à payer lors de la récupération : {$montant} FCFA.\n\n"
            .'Nous sommes à votre disposition pour la restitution. À très bientôt !';

        return $this->buildUrl($phone, $message);
    }

    /**
     * 3. Technical inspection reminder at J-7.
     */
    public function urlRappelVisiteJMoins7(Vehicule $vehicule): string
    {
        $client = $vehicule->client;
        $clientNom = $client?->nom ?? 'Cher(e) Client(e)';
        $phone = $client?->telephone ?? '';
        $dateExp = $vehicule->date_visite_technique ? $vehicule->date_visite_technique->format('d/m/Y') : 'prochainement';

        $message = "Bonjour {$clientNom},\n\n"
            ."Nous vous informons que la visite technique de votre véhicule {$vehicule->immatriculation} ({$vehicule->marque_modele}) arrive à échéance dans 7 jours (le {$dateExp}).\n"
            ."Pensez à planifier votre passage au centre de contrôle technique pour rouler en toute sécurité et éviter les pénalités.\n\n"
            .'Bien cordialement, votre station de lavage.';

        return $this->buildUrl($phone, $message);
    }

    /**
     * 4. Technical inspection reminder at J-3 with pre-inspection assistance offer.
     */
    public function urlRappelVisiteJMoins3(Vehicule $vehicule): string
    {
        $client = $vehicule->client;
        $clientNom = $client?->nom ?? 'Cher(e) Client(e)';
        $phone = $client?->telephone ?? '';
        $dateExp = $vehicule->date_visite_technique ? $vehicule->date_visite_technique->format('d/m/Y') : 'dans 3 jours';

        $message = "Bonjour {$clientNom},\n\n"
            ."Rappel important : la visite technique de votre véhicule {$vehicule->immatriculation} ({$vehicule->marque_modele}) expire dans 3 jours seulement (le {$dateExp}).\n\n"
            ."Notre station propose un service complet de préparation, nettoyage châssis/moteur et assistance pour faciliter votre passage au contrôle technique.\n"
            ."Contactez-nous ou passez nous voir dès aujourd'hui !";

        return $this->buildUrl($phone, $message);
    }

    /**
     * 5. Technical inspection urgent reminder at Day D.
     */
    public function urlRappelVisiteJourJ(Vehicule $vehicule): string
    {
        $client = $vehicule->client;
        $clientNom = $client?->nom ?? 'Cher(e) Client(e)';
        $phone = $client?->telephone ?? '';

        $message = "URGENT - Bonjour {$clientNom},\n\n"
            ."La visite technique de votre véhicule {$vehicule->immatriculation} ({$vehicule->marque_modele}) expire AUJOURD'HUI.\n"
            ."Afin d'éviter toute contravention ou mise en fourrière lors des contrôles routiers, nous vous recommandons d'effectuer votre renouvellement sans délai.\n\n"
            ."Votre équipe d'entretien et lavage automobile.";

        return $this->buildUrl($phone, $message);
    }

    /**
     * 6. Loyalty tier reached notification.
     */
    public function urlFidelite(Client $client, RegleFidelite $regle): string
    {
        $phone = $client->telephone;
        $avantageDesc = $regle->description;

        $message = "Félicitations {$client->nom} !\n\n"
            ."Grâce à votre fidélité, vous avez franchi le palier de {$regle->seuil_visites} visites chez nous.\n"
            ."Vous bénéficiez du privilège suivant : {$avantageDesc}.\n\n"
            .'Présentez ce message lors de votre prochain passage pour profiter de votre avantage. Merci infiniment de votre fidélité !';

        return $this->buildUrl($phone, $message);
    }
}
