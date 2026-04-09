<?php

namespace MonPackage\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use MonPackage\Ecommerce\Events\CommandePassee;
use MonPackage\Ecommerce\Mail\ConfirmationCommande;
use Illuminate\Support\Facades\Mail;

class EnvoyerConfirmationCommande implements ShouldQueue
{
    public function handle(CommandePassee $event): void
    {
        $commande = $event->commande->load(['items', 'client']);
        $email    = $commande->adresse_livraison['email'] ?? $commande->client?->email;

        if ($email && config('ecommerce.notifications.client_confirmation')) {
            Mail::to($email)->send(new ConfirmationCommande($commande));
        }

        if (config('ecommerce.notifications.admin_nouvelle_commande')) {
            Mail::to(config('ecommerce.boutique.email'))
                ->send(new \MonPackage\Ecommerce\Mail\NouvelleCommandeAdmin($commande));
        }
    }
}
