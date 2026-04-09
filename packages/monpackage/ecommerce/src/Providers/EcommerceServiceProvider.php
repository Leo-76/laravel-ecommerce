<?php

namespace MonPackage\Ecommerce\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use MonPackage\Ecommerce\Commands\InstallEcommerce;
use MonPackage\Ecommerce\Commands\MakeAdmin;
use MonPackage\Ecommerce\Http\Middleware\AdminEcommerce;
use MonPackage\Ecommerce\Services\PanierService;
use MonPackage\Ecommerce\Services\BoutiqueService;

class EcommerceServiceProvider extends ServiceProvider
{
    /**
     * Enregistre les services du package.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/ecommerce.php',
            'ecommerce'
        );

        // Liaison des services (singleton pour le panier en session)
        $this->app->singleton('panier', function ($app) {
            return new PanierService($app['session'], $app['config']);
        });

        $this->app->singleton('boutique', function ($app) {
            return new BoutiqueService($app['config']);
        });
    }

    /**
     * Bootstrap des services du package.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerMigrations();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerPublishables();
        $this->registerMiddleware();
        $this->registerBladeDirectives();
        $this->registerObservers();
        $this->registerEvents();
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallEcommerce::class,
                MakeAdmin::class,
            ]);
        }
    }

    protected function registerMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        }
    }

    protected function registerRoutes(): void
    {
        // Routes boutique front
        Route::middleware(config('ecommerce.middleware.web', ['web']))
            ->prefix(config('ecommerce.prefix.shop', 'boutique'))
            ->name('ecommerce.')
            ->group(__DIR__ . '/../Routes/web.php');

        // Routes API
        Route::middleware(config('ecommerce.middleware.api', ['api']))
            ->prefix('api/' . config('ecommerce.prefix.api', 'ecommerce'))
            ->name('ecommerce.api.')
            ->group(__DIR__ . '/../Routes/api.php');

        // Routes admin
        Route::middleware(config('ecommerce.middleware.admin', ['web', 'auth', 'ecommerce.admin']))
            ->prefix(config('ecommerce.prefix.admin', 'admin/boutique'))
            ->name('ecommerce.admin.')
            ->group(__DIR__ . '/../Routes/admin.php');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'ecommerce');
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'ecommerce');
    }

    protected function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            // Config
            $this->publishes([
                __DIR__ . '/../Config/ecommerce.php' => config_path('ecommerce.php'),
            ], 'ecommerce-config');

            // Migrations
            $this->publishes([
                __DIR__ . '/../Migrations/' => database_path('migrations'),
            ], 'ecommerce-migrations');

            // Vues
            $this->publishes([
                __DIR__ . '/../Resources/views' => resource_path('views/vendor/ecommerce'),
            ], 'ecommerce-views');

            // Traductions
            $this->publishes([
                __DIR__ . '/../Resources/lang' => lang_path('vendor/ecommerce'),
            ], 'ecommerce-lang');

            // Stubs (assets publics)
            $this->publishes([
                __DIR__ . '/../../stubs/public' => public_path('vendor/ecommerce'),
            ], 'ecommerce-assets');

            // Tout publier en une fois
            $this->publishes([
                __DIR__ . '/../Config/ecommerce.php'   => config_path('ecommerce.php'),
                __DIR__ . '/../Migrations/'             => database_path('migrations'),
                __DIR__ . '/../Resources/views'         => resource_path('views/vendor/ecommerce'),
                __DIR__ . '/../Resources/lang'          => lang_path('vendor/ecommerce'),
            ], 'ecommerce');
        }
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('ecommerce.admin', AdminEcommerce::class);
    }

    protected function registerBladeDirectives(): void
    {
        // @prixFormate(1999) → "19,99 €"
        Blade::directive('prixFormate', function ($expression) {
            return "<?php echo \\MonPackage\\Ecommerce\\Helpers\\PrixHelper::formater($expression); ?>";
        });

        // @estAdmin
        Blade::if('estAdmin', function () {
            return auth()->check() && auth()->user()->hasRole('ecommerce-admin');
        });

        // @panierVide
        Blade::if('panierVide', function () {
            return app('panier')->estVide();
        });
    }

    protected function registerObservers(): void
    {
        \MonPackage\Ecommerce\Models\Produit::observe(
            \MonPackage\Ecommerce\Observers\ProduitObserver::class
        );
        \MonPackage\Ecommerce\Models\Commande::observe(
            \MonPackage\Ecommerce\Observers\CommandeObserver::class
        );
    }

    protected function registerEvents(): void
    {
        $events = $this->app['events'];

        $events->listen(
            \MonPackage\Ecommerce\Events\CommandePassee::class,
            \MonPackage\Ecommerce\Listeners\EnvoyerConfirmationCommande::class
        );

        $events->listen(
            \MonPackage\Ecommerce\Events\CommandePassee::class,
            \MonPackage\Ecommerce\Listeners\ReduireStockProduits::class
        );

        $events->listen(
            \MonPackage\Ecommerce\Events\StockFaible::class,
            \MonPackage\Ecommerce\Listeners\NotifierAdminStockFaible::class
        );
    }
}
