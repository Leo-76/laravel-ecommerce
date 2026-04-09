<?php

namespace MonPackage\Ecommerce\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MonPackage\Ecommerce\Models\Commande;

class CommandePassee
{
    use Dispatchable, SerializesModels;
    public function __construct(public readonly Commande $commande) {}
}
