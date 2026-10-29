<?php

namespace App\Mail;

use App\Models\ClientFideliteAvantage;
use App\Models\Facture;
use App\Models\RegleFidelite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketEncaisseMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Facture $facture,
        public ?RegleFidelite $regleDebloquee = null,
        public ?ClientFideliteAvantage $avantageDebloque = null,
    ) {
        $this->facture->loadMissing(['pressing', 'ligneFactures.service', 'vehicule', 'client', 'fideliteAvantage.regleFidelite']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->facture->isLavage()
            ? 'Reçu de paiement & Restitution véhicule — Ticket N° '.$this->facture->num_ticket
            : 'Reçu de paiement & Retrait vêtements — Ticket N° '.$this->facture->num_ticket;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket_encaisse',
        );
    }
}
