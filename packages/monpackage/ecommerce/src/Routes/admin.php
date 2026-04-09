<?php

use Illuminate\Support\Facades\Route;
use MonPackage\Ecommerce\Http\Controllers\Admin\DashboardController;
use MonPackage\Ecommerce\Http\Controllers\Admin\ProduitAdminController;
use MonPackage\Ecommerce\Http\Controllers\Admin\CategorieAdminController;
use MonPackage\Ecommerce\Http\Controllers\Admin\CommandeAdminController;
use MonPackage\Ecommerce\Http\Controllers\Admin\CouponAdminController;
use MonPackage\Ecommerce\Http\Controllers\Admin\AvisAdminController;
use MonPackage\Ecommerce\Http\Controllers\Admin\ParametresController;

// ── Dashboard ─────────────────────────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');

// ── Produits ─────────────────────────────────────────────────────────────────
Route::resource('produits', ProduitAdminController::class);
Route::post('produits/{id}/toggle-actif', [ProduitAdminController::class, 'toggleActif'])->name('produits.toggle');
Route::post('produits/{id}/image', [ProduitAdminController::class, 'uploadImage'])->name('produits.image');
Route::delete('produits/{id}/image/{imageId}', [ProduitAdminController::class, 'supprimerImage'])->name('produits.image.delete');
Route::post('produits/import', [ProduitAdminController::class, 'import'])->name('produits.import');
Route::get('produits/export', [ProduitAdminController::class, 'export'])->name('produits.export');

// ── Catégories ────────────────────────────────────────────────────────────────
Route::resource('categories', CategorieAdminController::class);
Route::post('categories/reordonner', [CategorieAdminController::class, 'reordonner'])->name('categories.reordonner');

// ── Commandes ─────────────────────────────────────────────────────────────────
Route::prefix('commandes')->name('commandes.')->group(function () {
    Route::get('/',                    [CommandeAdminController::class, 'index'])->name('index');
    Route::get('/{reference}',         [CommandeAdminController::class, 'show'])->name('show');
    Route::patch('/{reference}/statut', [CommandeAdminController::class, 'changerStatut'])->name('statut');
    Route::post('/{reference}/expedition', [CommandeAdminController::class, 'expedier'])->name('expedier');
    Route::post('/{reference}/remboursement', [CommandeAdminController::class, 'rembourser'])->name('rembourser');
    Route::get('/{reference}/facture', [CommandeAdminController::class, 'facture'])->name('facture');
    Route::get('/export',              [CommandeAdminController::class, 'export'])->name('export');
});

// ── Coupons ───────────────────────────────────────────────────────────────────
Route::resource('coupons', CouponAdminController::class);

// ── Avis ──────────────────────────────────────────────────────────────────────
Route::prefix('avis')->name('avis.')->group(function () {
    Route::get('/',            [AvisAdminController::class, 'index'])->name('index');
    Route::patch('/{id}/approuver', [AvisAdminController::class, 'approuver'])->name('approuver');
    Route::patch('/{id}/rejeter',   [AvisAdminController::class, 'rejeter'])->name('rejeter');
    Route::delete('/{id}',     [AvisAdminController::class, 'destroy'])->name('destroy');
});

// ── Paramètres ────────────────────────────────────────────────────────────────
Route::get('/parametres',   [ParametresController::class, 'index'])->name('parametres');
Route::put('/parametres',   [ParametresController::class, 'update'])->name('parametres.update');
