# Laravel Ecommerce App

Application Laravel 12 complète avec e-commerce intégré — boutique, panier, commandes, panel admin.

## Installation

```bash
# 1. Cloner le projet
git clone https://github.com/TON_USERNAME/laravel-ecommerce-app.git
cd laravel-ecommerce-app

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Créer la base de données SQLite (ou configurer MySQL/PostgreSQL dans .env)
touch database/database.sqlite

# 5. Installer le package e-commerce
php artisan e-commerce:install

# 6. Migrer la base de données
php artisan migrate

# 7. Données de démo
php artisan db:seed --force

# 8. Lancer le serveur
php artisan serve
```

## Comptes de démonstration

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| admin@exemple.com | password | Admin + Admin boutique |
| user@exemple.com | password | Utilisateur |

## URLs

| URL | Description |
|-----|-------------|
| `/` | Page d'accueil |
| `/login` | Connexion |
| `/register` | Inscription |
| `/dashboard` | Dashboard |
| `/boutique` | Boutique |
| `/boutique/produits` | Catalogue |
| `/boutique/panier` | Panier |
| `/admin` | Panel admin app |
| `/admin/boutique` | Panel admin e-commerce |
| `/api/ecommerce/produits` | API REST |

## Stack technique

- **Laravel** 12
- **PHP** 8.2+
- **Base de données** SQLite (défaut) / MySQL / PostgreSQL
- **CSS** Tailwind CSS (CDN)
- **JS** Alpine.js
- **Package e-commerce** monpackage/ecommerce (local)

## Structure

```
app/
├── Http/Controllers/
│   ├── Auth/LoginController.php
│   ├── Auth/RegisterController.php
│   ├── AdminController.php
│   └── DashboardController.php
├── Models/User.php
└── Providers/AppServiceProvider.php

packages/
└── monpackage/ecommerce/    ← package e-commerce intégré

resources/views/
├── layouts/app.blade.php
├── auth/login.blade.php
├── auth/register.blade.php
├── dashboard.blade.php
├── welcome.blade.php
└── admin/
    ├── index.blade.php
    └── utilisateurs.blade.php
```
