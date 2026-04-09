<?php

use Illuminate\Support\Facades\Route;
use MonPackage\Ecommerce\Http\Controllers\ShopController;
use MonPackage\Ecommerce\Http\Controllers\ProduitController;
use MonPackage\Ecommerce\Http\Controllers\PanierController;
use MonPackage\Ecommerce\Http\Controllers\CheckoutController;
use MonPackage\Ecommerce\Http\Controllers\CompteController;
use MonPackage\Ecommerce\Http\Controllers\WishlistController;
use MonPackage\Ecommerce\Http\Controllers\AvisController;

// ── Accueil boutique ──────────────────────────────────────────────────────────
Route::get('/', [ShopController::class, 'index'])->name('home');

// ── Catalogue & Produits ──────────────────────────────────────────────────────
Route::prefix('produits')->name('produits.')->group(function () {
    Route::get('/',                    [ProduitController::class, 'index'])->name('index');
    Route::get('/recherche',           [ProduitController::class, 'recherche'])->name('recherche');
    Route::get('/promotions',          [ProduitController::class, 'promotions'])->name('promotions');
    Route::get('/nouveautes',          [ProduitController::class, 'nouveautes'])->name('nouveautes');
    Route::get('/{slug}',              [ProduitController::class, 'show'])->name('show');
    Route::get('/{slug}/stock',        [ProduitController::class, 'stock'])->name('stock');
});

// ── Catégories ────────────────────────────────────────────────────────────────
Route::get('/categorie/{slug}', [ProduitController::class, 'categorie'])->name('categorie');
Route::get('/categorie/{slug}/{sous}', [ProduitController::class, 'sousCategorie'])->name('sous-categorie');

// ── Panier ────────────────────────────────────────────────────────────────────
Route::prefix('panier')->name('panier.')->group(function () {
    Route::get('/',                [PanierController::class, 'index'])->name('index');
    Route::post('/ajouter',        [PanierController::class, 'ajouter'])->name('ajouter');
    Route::patch('/{cle}',         [PanierController::class, 'modifier'])->name('modifier');
    Route::delete('/{cle}',        [PanierController::class, 'supprimer'])->name('supprimer');
    Route::delete('/',             [PanierController::class, 'vider'])->name('vider');
    Route::post('/coupon',         [PanierController::class, 'appliquerCoupon'])->name('coupon.appliquer');
    Route::delete('/coupon',       [PanierController::class, 'retirerCoupon'])->name('coupon.retirer');
    Route::get('/mini',            [PanierController::class, 'mini'])->name('mini');
});

// ── Checkout ─────────────────────────────────────────────────────────────────
Route::prefix('commande')->name('commande.')->group(function () {
    Route::get('/livraison',       [CheckoutController::class, 'livraison'])->name('livraison');
    Route::post('/livraison',      [CheckoutController::class, 'saveLivraison'])->name('livraison.save');
    Route::get('/paiement',        [CheckoutController::class, 'paiement'])->name('paiement');
    Route::post('/paiement',       [CheckoutController::class, 'processerPaiement'])->name('paiement.processer');
    Route::get('/recapitulatif',   [CheckoutController::class, 'recapitulatif'])->name('recap');
    Route::get('/confirmation/{reference}', [CheckoutController::class, 'confirmation'])->name('confirmation');
    Route::post('/webhook/{passerelle}', [CheckoutController::class, 'webhook'])->name('webhook')
        ->withoutMiddleware(['web'])->middleware(['api']);
});

// ── Avis ──────────────────────────────────────────────────────────────────────
Route::prefix('avis')->name('avis.')->middleware('auth')->group(function () {
    Route::post('/{produitSlug}',  [AvisController::class, 'store'])->name('store');
    Route::delete('/{id}',         [AvisController::class, 'destroy'])->name('destroy');
});

// ── Wishlist ─────────────────────────────────────────────────────────────────
Route::prefix('wishlist')->name('wishlist.')->middleware('auth')->group(function () {
    Route::get('/',                [WishlistController::class, 'index'])->name('index');
    Route::post('/{produitId}',    [WishlistController::class, 'toggle'])->name('toggle');
});

// ── Compte client ─────────────────────────────────────────────────────────────
Route::prefix('compte')->name('compte.')->middleware('auth')->group(function () {
    Route::get('/commandes',            [CompteController::class, 'commandes'])->name('commandes');
    Route::get('/commandes/{reference}', [CompteController::class, 'detailCommande'])->name('commande');
    Route::post('/commandes/{reference}/retour', [CompteController::class, 'demandeRetour'])->name('retour');
    Route::get('/profil',               [CompteController::class, 'profil'])->name('profil');
    Route::put('/profil',               [CompteController::class, 'updateProfil'])->name('profil.update');
    Route::get('/adresses',             [CompteController::class, 'adresses'])->name('adresses');
});
