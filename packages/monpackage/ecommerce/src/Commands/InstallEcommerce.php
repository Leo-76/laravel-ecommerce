<?php

namespace MonPackage\Ecommerce\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallEcommerce extends Command
{
    protected $signature = 'e-commerce:install
                            {--sans-migrations : Ignore les migrations}
                            {--sans-seeders    : Ignore les seeders de démo}
                            {--sans-vues       : Ne publie pas les vues}
                            {--force           : Écrase les fichiers existants}
                            {--demo            : Installe avec des données de démonstration}';

    protected $description = 'Installe le package e-commerce Laravel — migrations, config, routes et vues.';

    // Couleurs ANSI pour un beau terminal
    private string $logo = <<<LOGO
    ╔══════════════════════════════════════════════════════╗
    ║                                                      ║
    ║   🛒  MonPackage / E-Commerce  v1.0                  ║
    ║       Package e-commerce Laravel maison              ║
    ║                                                      ║
    ╚══════════════════════════════════════════════════════╝
    LOGO;

    public function handle(): int
    {
        $this->newLine();
        $this->line("<fg=cyan>{$this->logo}</>");
        $this->newLine();

        // ─── Étape 1 : Publication de la configuration ──────────────────
        $this->etape('1/5', 'Publication de la configuration...');

        Artisan::call('vendor:publish', [
            '--tag'   => 'ecommerce-config',
            '--force' => $this->option('force'),
        ]);

        $this->success('config/ecommerce.php publié');

        // ─── Étape 2 : Migrations ─────────────────────────────────────
        if (! $this->option('sans-migrations')) {
            $this->etape('2/5', 'Publication et exécution des migrations...');

            Artisan::call('vendor:publish', [
                '--tag'   => 'ecommerce-migrations',
                '--force' => $this->option('force'),
            ]);

            $this->info('  ↳ Migrations publiées dans database/migrations/');

            if ($this->confirm('  Voulez-vous exécuter les migrations maintenant ?', true)) {
                Artisan::call('migrate');
                $this->success('Migrations exécutées avec succès');
            } else {
                $this->warn('  ⚠  Pensez à exécuter `php artisan migrate` manuellement.');
            }
        } else {
            $this->etape('2/5', 'Migrations ignorées (--sans-migrations)');
        }

        // ─── Étape 3 : Seeders de démo ───────────────────────────────
        if (! $this->option('sans-seeders') && $this->option('demo')) {
            $this->etape('3/5', 'Insertion des données de démonstration...');

            Artisan::call('db:seed', [
                '--class' => \MonPackage\Ecommerce\Database\Seeders\EcommerceDemoSeeder::class,
                '--force' => true,
            ]);

            $this->success('20 produits de démo insérés');
        } else {
            $this->etape('3/5', 'Seeders ignorés (utilisez --demo pour les données de test)');
        }

        // ─── Étape 4 : Publication des vues ──────────────────────────
        if (! $this->option('sans-vues')) {
            $this->etape('4/5', 'Publication des vues Blade...');

            Artisan::call('vendor:publish', [
                '--tag'   => 'ecommerce-views',
                '--force' => $this->option('force'),
            ]);

            $this->success('Vues publiées dans resources/views/vendor/ecommerce/');
        } else {
            $this->etape('4/5', 'Vues ignorées (--sans-vues)');
        }

        // ─── Étape 5 : Vérifications finales ─────────────────────────
        $this->etape('5/5', 'Vérifications finales...');
        $this->verifierEnvironnement();

        // ─── Récapitulatif ────────────────────────────────────────────
        $this->newLine();
        $this->line('<fg=green>╔══════════════════════════════════════════════════════╗</>');
        $this->line('<fg=green>║   ✅  Installation terminée avec succès !            ║</>');
        $this->line('<fg=green>╚══════════════════════════════════════════════════════╝</>');
        $this->newLine();

        $prefixShop  = config('ecommerce.prefix.shop', 'boutique');
        $prefixAdmin = config('ecommerce.prefix.admin', 'admin/boutique');

        $this->table(
            ['URL', 'Description'],
            [
                ["/{$prefixShop}",            'Boutique front-end'],
                ["/{$prefixShop}/produits",   'Catalogue produits'],
                ["/{$prefixShop}/panier",     'Panier d\'achat'],
                ["/{$prefixShop}/commande",   'Checkout'],
                ["/{$prefixAdmin}",           'Panel d\'administration'],
                ["/api/ecommerce/produits",   'API REST Produits'],
            ]
        );

        $this->newLine();
        $this->line('<fg=yellow>Prochaines étapes :</>');
        $this->line('  1. Configurez votre passerelle de paiement dans <fg=cyan>config/ecommerce.php</>');
        $this->line('  2. Personnalisez vos vues dans <fg=cyan>resources/views/vendor/ecommerce/</>');
        $this->line('  3. Créez un admin avec <fg=cyan>php artisan e-commerce:make-admin</>');
        $this->newLine();

        return Command::SUCCESS;
    }

    private function etape(string $num, string $message): void
    {
        $this->newLine();
        $this->line("  <fg=blue;options=bold>[{$num}]</> {$message}");
    }

    private function success(string $message): void
    {
        $this->line("       <fg=green>✓</> {$message}");
    }

    private function verifierEnvironnement(): void
    {
        $checks = [
            'Clé APP_KEY définie'         => strlen(config('app.key', '')) > 0,
            'Base de données configurée'  => $this->testConnexionBDD(),
            'Storage lié (storage:link)'  => file_exists(public_path('storage')),
            'Queue configurée'            => config('queue.default') !== 'sync',
        ];

        foreach ($checks as $label => $ok) {
            $icon   = $ok ? '<fg=green>✓</>' : '<fg=yellow>⚠</>';
            $status = $ok ? '<fg=green>OK</>'  : '<fg=yellow>À vérifier</>';
            $this->line("       {$icon} {$label}: {$status}");
        }

        if (! file_exists(public_path('storage'))) {
            $this->warn('       Exécutez `php artisan storage:link` pour les images produits.');
        }
    }

    private function testConnexionBDD(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
