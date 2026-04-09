<?php

namespace MonPackage\Ecommerce\Observers;

use MonPackage\Ecommerce\Models\Commande;
use MonPackage\Ecommerce\Events\CommandePassee;

class CommandeObserver
{
    public function created(Commande $commande): void
    {
        event(new CommandePassee($commande));
    }

    public function updated(Commande $commande): void
    {
        // Incrémenter le compteur d'utilisations du coupon si payé
        if ($commande->isDirty('statut_paiement')
            && $commande->statut_paiement === Commande::PAIEMENT_PAYE
            && $commande->coupon_code) {
            \MonPackage\Ecommerce\Models\Coupon::where('code', $commande->coupon_code)
                ->increment('utilisations_count');
        }
    }
}
