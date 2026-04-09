<?php

namespace MonPackage\Ecommerce\Observers;

use MonPackage\Ecommerce\Models\Produit;

class ProduitObserver
{
    public function creating(Produit $produit): void
    {
        if (empty($produit->sku)) {
            $produit->sku = 'SKU-' . strtoupper(substr(md5(microtime()), 0, 8));
        }
    }

    public function deleting(Produit $produit): void
    {
        // Nettoyer les images du disque lors d'une suppression définitive
        if ($produit->isForceDeleting()) {
            foreach ($produit->images as $image) {
                \Storage::disk(config('ecommerce.images.disque', 'public'))->delete($image->chemin);
            }
            if ($produit->image_principale) {
                \Storage::disk(config('ecommerce.images.disque', 'public'))->delete($produit->image_principale);
            }
        }
    }
}
