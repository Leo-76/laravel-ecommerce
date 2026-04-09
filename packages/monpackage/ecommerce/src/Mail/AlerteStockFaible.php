<?php

namespace MonPackage\Ecommerce\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use MonPackage\Ecommerce\Models\Produit;

class AlerteStockFaible extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Produit $produit) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Stock faible : ' . $this->produit->nom . ' (' . $this->produit->stock . ' restant(s))',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'ecommerce::mail.alerte-stock-faible');
    }
}
