<?php

namespace App\Mail;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketPretMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Facture $facture)
    {
        $this->facture->loadMissing(['pressing', 'ligneFactures.service', 'vehicule']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->facture->isLavage()
            ? 'Votre véhicule est PRÊT ! — Ticket N° '.$this->facture->num_ticket
            : 'Vos vêtements sont PRÊTS ! — Ticket N° '.$this->facture->num_ticket;

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
            view: 'emails.ticket_pret',
        );
    }
}
