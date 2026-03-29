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
| API Routes Publiques - Catalogue
|--------------------------------------------------------------------------
|
| Routes publiques pour le frontend Vue.js catalogue client
|
*/

// Liste des bougies
Route::get('/bougies', [\App\Http\Controllers\Api\CatalogueController::class, 'index'])->name('api.bougies.index');

// Détail par référence (pattern BOUG-XXX)
Route::get('/bougies/{reference}', [\App\Http\Controllers\Api\CatalogueController::class, 'show'])
    ->where('reference', '^BOUG-[0-9]+$')
    ->name('api.bougies.show');

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
Route::post('/cart/sync', [CartController::class, 'sync'])->name('api.cart.sync');
Route::patch('/cart/{reference}', [CartController::class, 'update'])->name('api.cart.update');
Route::delete('/cart/{reference}', [CartController::class, 'destroy'])->name('api.cart.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('api.cart.clear');

/**
 * API Routes - Commandes (checkout client)
 */
Route::post('/orders', [\App\Http\Controllers\Api\OrderController::class, 'store'])->name('api.orders.store');
Route::get('/orders/{reference}', [\App\Http\Controllers\Api\OrderController::class, 'show'])->name('api.orders.show');