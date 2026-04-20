<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ModeMarcheApiController;
use App\Http\Controllers\Api\CatalogueController;
use App\Http\Controllers\Api\CartController;

/*
|--------------------------------------------------------------------------
| API Routes pour Mode Marché
|--------------------------------------------------------------------------
|
| Ces routes retournent TOUJOURS du JSON. Elles sont séparées des routes
| web (Blade) pour éviter la confusion API/View.
|
| Middleware: 'auth:sanctum' + 'role:admin,employe'
|
*/

Route::middleware(['auth', 'role:admin,employe'])->prefix('marche')->name('api.marche.')->group(function () {
    // Liste des ventes du jour
    Route::get('/ventes-jour', [ModeMarcheApiController::class, 'ventesJour'])->name('ventes-jour');
    
    // Annuler une vente
    Route::post('/{order}/cancel', [ModeMarcheApiController::class, 'cancel'])->name('cancel');
    
    // Export CSV
    Route::get('/export', [ModeMarcheApiController::class, 'export'])->name('export');
});


/*
|--------------------------------------------------------------------------
| API Routes Publiques - REST API T5.4
|--------------------------------------------------------------------------
|
| Routes REST publiques avec rate limiting
|
*/

use App\Http\Controllers\Api\RestBougieController;
use App\Http\Controllers\Api\CategorieController;

// API REST /api/bougies avec rate limiting (60 req/min)
Route::middleware(['throttle:api'])->group(function () {
    // Liste paginée des bougies avec filtres
    Route::get('/bougies', [RestBougieController::class, 'index'])->name('api.bougies.index');

    // Détail par ID ou référence
    Route::get('/bougies/{identifier}', [RestBougieController::class, 'show'])
        ->name('api.bougies.show');

    // Liste des catégories/collections
    Route::get('/categories', [CategorieController::class, 'index'])->name('api.categories.index');
});

/*
|--------------------------------------------------------------------------
| API Routes Legacy - Catalogue
|--------------------------------------------------------------------------
*/

// Legacy: Liste des bougies (pour compatibilité)
Route::get('/catalogue/bougies', [\App\Http\Controllers\Api\CatalogueController::class, 'index'])->name('api.catalogue.bougies.index');

// Legacy: Détail avec référence
Route::get('/catalogue/bougies/{reference}', [\App\Http\Controllers\Api\CatalogueController::class, 'show'])
    ->where('reference', '^BOUG-[0-9]+$');

// API catalogue legacy (pour les tests)
Route::get('/catalogue/bougies', [\App\Http\Controllers\Api\CatalogueController::class, 'index'])->name('api.catalogue.bougies.index');

// Détail avec référence via /catalogue/bougies/{reference}
Route::get('/catalogue/bougies/{reference}', [\App\Http\Controllers\Api\CatalogueController::class, 'show'])
    ->where('reference', '^BOUG-[0-9]+$');

/**
 * API Routes - Panier (session-based, accessible sans auth)
 */
Route::get('/cart', [CartController::class, 'index'])->name('api.cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('api.cart.store');
Route::patch('/cart/{reference}', [CartController::class, 'update'])->name('api.cart.update');
Route::delete('/cart/{reference}', [CartController::class, 'destroy'])->name('api.cart.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('api.cart.clear');

/**
 * API Routes - Commandes (checkout client)
 */
Route::post('/orders', [\App\Http\Controllers\Api\OrderController::class, 'store'])->name('api.orders.store');
Route::get('/orders/{reference}', [\App\Http\Controllers\Api\OrderController::class, 'show'])->name('api.orders.show');