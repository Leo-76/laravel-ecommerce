<?php

namespace MonPackage\Ecommerce\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use MonPackage\Ecommerce\Models\Commande;

class ConfirmationCommande extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Commande $commande) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de votre commande ' . $this->commande->reference . ' — ' . config('ecommerce.boutique.nom'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'ecommerce::mail.confirmation-commande',
        );
    }
}
