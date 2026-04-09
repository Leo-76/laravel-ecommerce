<?php

namespace MonPackage\Ecommerce\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MonPackage\Ecommerce\Services\BoutiqueService
 */
class Boutique extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'boutique';
    }
}
