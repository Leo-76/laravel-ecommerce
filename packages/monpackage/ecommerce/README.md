# 🛒 MonPackage/Ecommerce

Un package e-commerce **Laravel maison**, complet et prêt à l'emploi.

## Fonctionnalités

- ✅ Catalogue produits (simple, variable, numérique)
- ✅ Catégories hiérarchiques
- ✅ Panier intelligent (session ou base de données)
- ✅ Checkout multi-étapes (livraison → paiement → confirmation)
- ✅ Paiement Stripe & PayPal intégré
- ✅ Gestion des stocks avec alertes
- ✅ Coupons de réduction (%, fixe, livraison gratuite)
- ✅ Avis clients avec modération
- ✅ Wishlist
- ✅ Panel d'administration complet
- ✅ Emails transactionnels
- ✅ API REST (Sanctum)
- ✅ Export CSV commandes & produits
- ✅ Multi-passerelles de paiement

---

## Installation

### 1. Installer via Composer

```bash
composer require monpackage/ecommerce
```

### 2. Lancer le wizard d'installation

```bash
php artisan e-commerce:install
```

Cette commande interactive va :
- Publier `config/ecommerce.php`
- Publier et exécuter les migrations (12 tables préfixées `eco_`)
- Publier les vues Blade dans `resources/views/vendor/ecommerce/`
- Effectuer des vérifications d'environnement

### 3. Options disponibles

```bash
# Installation complète avec données de démo
php artisan e-commerce:install --demo

# Ignorer les migrations (si déjà faites)
php artisan e-commerce:install --sans-migrations

# Ne pas publier les vues
php artisan e-commerce:install --sans-vues

# Écraser les fichiers existants
php artisan e-commerce:install --force
```

### 4. Créer un administrateur

```bash
php artisan e-commerce:make-admin admin@exemple.com
```

---

## Configuration

Après l'installation, éditez `config/ecommerce.php` :

```php
// Informations boutique
'boutique' => [
    'nom'     => 'Ma Super Boutique',
    'email'   => 'contact@maboutique.fr',
    'devise'  => 'EUR',
    'symbole' => '€',
],

// Passerelle de paiement
'paiement' => [
    'passerelle' => 'stripe', // stripe | paypal | virement | especes
],
```

Ou via `.env` :

```env
ECOMMERCE_NOM="Ma Super Boutique"
ECOMMERCE_EMAIL="contact@maboutique.fr"
ECOMMERCE_PASSERELLE=stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

---

## Structure des URLs

| URL | Description |
|-----|-------------|
| `/boutique` | Accueil boutique |
| `/boutique/produits` | Catalogue |
| `/boutique/produits/{slug}` | Fiche produit |
| `/boutique/panier` | Panier |
| `/boutique/commande/livraison` | Checkout – livraison |
| `/boutique/commande/paiement` | Checkout – paiement |
| `/boutique/compte/commandes` | Mes commandes |
| `/admin/boutique` | Panel admin |
| `/api/ecommerce/produits` | API REST |

---

## Utilisation dans vos vues

### Facade Panier

```php
use MonPackage\Ecommerce\Facades\Panier;

Panier::ajouter(produitId: 1, quantite: 2);
Panier::resume();   // Retourne sous-total, total, livraison...
Panier::total();    // int centimes
Panier::vider();
Panier::appliquerCoupon('PROMO20');
```

### Directives Blade

```blade
{{-- Afficher un prix formaté --}}
@prixFormate($produit->prix)   {{-- → 19,90 € --}}

{{-- Conditionnel admin --}}
@estAdmin
    <a href="/admin/boutique">Panel admin</a>
@endestAdmin

{{-- Conditionnel panier vide --}}
@panierVide
    <p>Votre panier est vide !</p>
@endpanierVide
```

### Modèles disponibles

```php
use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Categorie;
use MonPackage\Ecommerce\Models\Commande;
use MonPackage\Ecommerce\Models\Coupon;
use MonPackage\Ecommerce\Models\Avis;

// Scopes disponibles
Produit::actif()->enStock()->enPromo()->enVedette()->get();
Produit::recherche('iPhone')->paginate(12);

// Méthodes utiles
$produit->estEnStock();        // bool
$produit->estEnPromo();        // bool
$produit->aStockFaible();      // bool
$produit->prix_effectif;       // int (centimes, avec promo si applicable)
$produit->prix_formate;        // string → "19,90 €"
$produit->note_moyenne;        // float → 4.3

$commande->marquerPaye($txId); // Change statut + event
$commande->expedier($trans, $suivi);
$commande->changerStatut('expediee', 'Colis remis à La Poste');
```

---

## Événements

| Événement | Déclenché quand |
|-----------|-----------------|
| `CommandePassee` | Une nouvelle commande est créée |
| `StockFaible` | Un produit passe sous le seuil d'alerte |

```php
// Écouter un événement dans votre AppServiceProvider
use MonPackage\Ecommerce\Events\CommandePassee;

Event::listen(CommandePassee::class, function ($event) {
    $commande = $event->commande;
    // Votre logique...
});
```

---

## Personnalisation des vues

```bash
# Publier les vues pour les personnaliser
php artisan vendor:publish --tag=ecommerce-views
```

Les vues se trouvent alors dans `resources/views/vendor/ecommerce/`.

---

## API REST

Authentification via Laravel Sanctum.

```http
GET /api/ecommerce/produits?q=iphone&categorie=electronique&prix_max=500
GET /api/ecommerce/produits/{slug}
GET /api/ecommerce/categories

# Authentifié (Bearer token)
GET    /api/ecommerce/panier
POST   /api/ecommerce/panier        { produit_id, quantite, variation_id? }
PATCH  /api/ecommerce/panier/{cle}  { quantite }
DELETE /api/ecommerce/panier/{cle}
POST   /api/ecommerce/commandes
GET    /api/ecommerce/commandes/{reference}
```

---

## Structure des tables

Toutes les tables sont préfixées `eco_` pour éviter les conflits :

```
eco_categories          — Catégories (hiérarchiques)
eco_produits            — Produits
eco_produit_categorie   — Pivot produits ↔ catégories
eco_produit_images      — Images produits
eco_attributs           — Attributs (Taille, Couleur...)
eco_attribut_valeurs    — Valeurs d'attributs
eco_variations          — Variations de produits
eco_variation_attributs — Pivot variations ↔ attributs
eco_paniers             — Paniers persistants
eco_panier_items        — Articles du panier
eco_coupons             — Codes de réduction
eco_commandes           — Commandes
eco_commande_items      — Lignes de commande
eco_commande_historique — Historique des statuts
eco_avis                — Avis clients
eco_wishlist            — Liste de souhaits
eco_retours             — Demandes de retour
```

---

## Licence

MIT — libre d'utilisation, modification et distribution.
