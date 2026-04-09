<?php

namespace MonPackage\Ecommerce\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use MonPackage\Ecommerce\Models\Commande;

class NouvelleCommandeAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Commande $commande) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🛒 Nouvelle commande : ' . $this->commande->reference,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'ecommerce::mail.nouvelle-commande-admin');
    }
}
