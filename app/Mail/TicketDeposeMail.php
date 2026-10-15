<?php

namespace App\Mail;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketDeposeMail extends Mailable
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
            ? 'Prise en charge de votre véhicule — Ticket N° '.$this->facture->num_ticket
            : 'Confirmation de votre dépôt — Ticket N° '.$this->facture->num_ticket;

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
            view: 'emails.ticket_depose',
        );
    }
}
