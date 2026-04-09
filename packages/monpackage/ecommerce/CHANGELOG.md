# Changelog — MonPackage/Ecommerce

Toutes les modifications notables de ce package sont documentées ici.

## [1.0.0] — 2024-01-01

### Ajouté
- 🚀 Installation via `php artisan e-commerce:install` avec wizard interactif
- 👤 Commande `php artisan e-commerce:make-admin` pour créer des admins
- 🗄️ 17 tables SQL préfixées `eco_` (une seule migration)
- 🏷️ Modèle `Produit` avec scopes, traits `ASlug`, `APrix`, gestion promo, stock
- 📂 Modèle `Categorie` avec hiérarchie parent/enfant
- 📦 Modèle `Commande` avec machine à états, référence auto-générée
- 🛒 `PanierService` avec session, calcul livraison gratuite, coupons
- 💳 `PaiementService` Stripe + PayPal + virement + espèces
- 🎫 Coupons de réduction (%, fixe, livraison offerte)
- ⭐ Avis clients avec modération
- ❤️ Wishlist
- ↩️ Demandes de retour
- 🔔 Events : `CommandePassee`, `StockFaible`
- 📧 Emails : confirmation client, nouvelle commande admin, alerte stock
- 🌐 Routes boutique, admin, API REST (Sanctum)
- 🎨 Vues Blade complètes (Tailwind CSS + Alpine.js)
- 🖥️ Panel admin : dashboard KPIs, produits, commandes, coupons, avis
- 🖨️ Facture imprimable HTML
- 📊 Export CSV commandes & produits
- 🌱 Seeder de démo (20 produits, 5 catégories)
- 🧪 Tests PHPUnit (Feature tests)
- 📚 Documentation complète
