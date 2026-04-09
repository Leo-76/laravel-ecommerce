<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Gates
        Gate::define('admin', fn(User $u) => $u->isAdmin());
        Gate::define('super-admin', fn(User $u) => $u->isSuperAdmin());
        Gate::define('ecommerce-admin', fn(User $u) => $u->ecommerce_admin || $u->isAdmin());

        // Partager le résumé du panier avec toutes les vues
        View::composer('*', function ($view) {
            if (app()->has('panier')) {
                try {
                    $view->with('_panierResume', app('panier')->resume());
                } catch (\Throwable) {
                    $view->with('_panierResume', ['nombre_articles' => 0, 'total' => 0]);
                }
            }
        });
    }
}
