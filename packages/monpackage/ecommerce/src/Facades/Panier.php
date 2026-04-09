<?php

namespace MonPackage\Ecommerce\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array  ajouter(int $produitId, int $quantite = 1, ?int $variationId = null, array $options = [])
 * @method static array  modifier(string $cle, int $quantite)
 * @method static array  supprimer(string $cle)
 * @method static void   vider()
 * @method static array  appliquerCoupon(string $code)
 * @method static array  retirerCoupon()
 * @method static array  items()
 * @method static int    count()
 * @method static bool   estVide()
 * @method static int    sousTotal()
 * @method static int    remise()
 * @method static int    fraisLivraison()
 * @method static int    tva()
 * @method static int    total()
 * @method static array  resume()
 * @method static array|null couponActif()
 *
 * @see \MonPackage\Ecommerce\Services\PanierService
 */
class Panier extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'panier';
    }
}
