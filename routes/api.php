<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ModeMarcheApiController;

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

use App\Http\Controllers\Api\CatalogueController;

Route::get('/bougies', [CatalogueController::class, 'index'])->name('api.bougies.index');

// Route pour récupérer par référence (champ unique)
Route::get('/bougies/ref/{reference}', [CatalogueController::class, 'show'])->name('api.bougies.show');

// Route pour récupérer par ID
Route::get('/bougies/{bougie}', [CatalogueController::class, 'detail'])->name('api.bougies.detail');

// Route pour bougies similaires
Route::get('/bougies/{bougie}/similaires', [CatalogueController::class, 'similaires'])->name('api.bougies.similaires');

/**
 * API Routes - Panier (session-based, accessible sans auth)
 */
use App\Http\Controllers\Api\CartController;

Route::get('/cart', [CartController::class, 'index'])->name('api.cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('api.cart.store');
Route::patch('/cart/{reference}', [CartController::class, 'update'])->name('api.cart.update');
Route::delete('/cart/{reference}', [CartController::class, 'destroy'])->name('api.cart.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('api.cart.clear');
