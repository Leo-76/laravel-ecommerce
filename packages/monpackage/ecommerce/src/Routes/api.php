<?php

use Illuminate\Support\Facades\Route;
use MonPackage\Ecommerce\Http\Controllers\Api\ProduitApiController;
use MonPackage\Ecommerce\Http\Controllers\Api\CategorieApiController;
use MonPackage\Ecommerce\Http\Controllers\Api\PanierApiController;
use MonPackage\Ecommerce\Http\Controllers\Api\CommandeApiController;

// ── API Publique ──────────────────────────────────────────────────────────────
Route::get('/produits',              [ProduitApiController::class, 'index']);
Route::get('/produits/{slug}',       [ProduitApiController::class, 'show']);
Route::get('/categories',            [CategorieApiController::class, 'index']);
Route::get('/categories/{slug}',     [CategorieApiController::class, 'show']);

// ── API Authentifiée ──────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    // Panier
    Route::get('/panier',            [PanierApiController::class, 'index']);
    Route::post('/panier',           [PanierApiController::class, 'ajouter']);
    Route::patch('/panier/{cle}',    [PanierApiController::class, 'modifier']);
    Route::delete('/panier/{cle}',   [PanierApiController::class, 'supprimer']);
    Route::post('/panier/coupon',    [PanierApiController::class, 'coupon']);

    // Commandes
    Route::get('/commandes',         [CommandeApiController::class, 'index']);
    Route::get('/commandes/{ref}',   [CommandeApiController::class, 'show']);
    Route::post('/commandes',        [CommandeApiController::class, 'store']);
});
