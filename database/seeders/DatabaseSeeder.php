<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@exemple.com'], [
            'name'             => 'Admin',
            'password'         => Hash::make('password'),
            'role'             => 'admin',
            'ecommerce_admin'  => true,
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate(['email' => 'user@exemple.com'], [
            'name'             => 'Utilisateur',
            'password'         => Hash::make('password'),
            'role'             => 'user',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✓ Utilisateurs créés');

        $this->call(\MonPackage\Ecommerce\Database\Seeders\EcommerceDemoSeeder::class);

        $this->command->newLine();
        $this->command->info('┌─────────────────────────────────────────┐');
        $this->command->info('│  admin@exemple.com   → admin (password)  │');
        $this->command->info('│  user@exemple.com    → user  (password)  │');
        $this->command->info('└─────────────────────────────────────────┘');
    }
}
