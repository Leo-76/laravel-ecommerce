<?php

namespace MonPackage\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use MonPackage\Ecommerce\Events\StockFaible;
use Illuminate\Support\Facades\Mail;

class NotifierAdminStockFaible implements ShouldQueue
{
    public function handle(StockFaible $event): void
    {
        if (! config('ecommerce.notifications.admin_stock_faible')) return;

        Mail::to(config('ecommerce.boutique.email'))
            ->send(new \MonPackage\Ecommerce\Mail\AlerteStockFaible($event->produit));
    }
}
