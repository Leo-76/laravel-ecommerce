<?php

namespace MonPackage\Ecommerce\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    protected $signature   = 'e-commerce:make-admin {email? : Email de l\'administrateur}';
    protected $description = 'Crée ou promouvoit un utilisateur en administrateur e-commerce.';

    public function handle(): int
    {
        $this->info('🔑 Création d\'un administrateur e-commerce');

        $userModel = config('auth.providers.users.model', \App\Models\User::class);

        $email = $this->argument('email') ?? $this->ask('Email de l\'utilisateur');

        $user = $userModel::where('email', $email)->first();

        if (! $user) {
            if ($this->confirm("Aucun compte trouvé pour {$email}. Créer un nouveau compte ?", true)) {
                $nom      = $this->ask('Nom complet');
                $password = $this->secret('Mot de passe');

                $user = $userModel::create([
                    'name'     => $nom,
                    'email'    => $email,
                    'password' => Hash::make($password),
                ]);

                $this->info("Utilisateur {$email} créé.");
            } else {
                $this->error('Opération annulée.');
                return Command::FAILURE;
            }
        }

        // Attribution du rôle admin (compatible avec ou sans Spatie)
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'ecommerce-admin']);
            $user->assignRole($role);
        } else {
            // Champ simple dans la table users si pas de Spatie
            $user->forceFill(['ecommerce_admin' => true])->save();
        }

        $this->info("✅ {$email} est maintenant administrateur e-commerce.");
        $this->line("   Accès admin : <fg=cyan>" . url(config('ecommerce.prefix.admin', 'admin/boutique')) . "</>");

        return Command::SUCCESS;
    }
}
