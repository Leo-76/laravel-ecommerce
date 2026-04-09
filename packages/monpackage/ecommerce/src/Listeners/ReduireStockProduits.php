<?php

namespace MonPackage\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use MonPackage\Ecommerce\Events\CommandePassee;

class ReduireStockProduits implements ShouldQueue
{
    public function handle(CommandePassee $event): void
    {
        if (! config('ecommerce.stock.activer_gestion')) return;

        foreach ($event->commande->items as $item) {
            if ($item->variation_id && $item->variation) {
                $item->variation->decrement('stock', $item->quantite);
            } elseif ($item->produit) {
                $item->produit->decrementerStock($item->quantite);
            }
        }
    }
}
