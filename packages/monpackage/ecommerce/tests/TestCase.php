<?php

namespace MonPackage\Ecommerce\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use MonPackage\Ecommerce\Providers\EcommerceServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../src/Migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [EcommerceServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('ecommerce.boutique.nom', 'Boutique Test');
        $app['config']->set('ecommerce.boutique.devise', 'EUR');
        $app['config']->set('ecommerce.boutique.symbole', '€');
        $app['config']->set('ecommerce.stock.activer_gestion', true);
        $app['config']->set('ecommerce.panier.driver', 'session');
    }
}
