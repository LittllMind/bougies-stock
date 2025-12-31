<?php

use App\Http\Controllers\VinyleController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\FondController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return redirect()->route('vinyles.index');
});

Route::get('/dashboard', function () {
    return redirect()->route('vinyles.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('vinyles', VinyleController::class);
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');

    // Gestion des fonds (liste + mise à jour)
    Route::resource('fonds', FondController::class)->only(['index', 'update']);

    Route::resource('ventes', VenteController::class);
});



// Gestion du Client et Panier

// routes/web.php

// Panier public (accessible sans connexion)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/{item}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});


Route::prefix('kiosque')->name('kiosque.')->group(function () {
    Route::get('/', [VinyleController::class, 'kiosque'])->name('index');
    Route::post('/vendre', [VenteController::class, 'storeFromKiosque'])->name('vendre');
});


Route::get('/orders/create', [OrderController::class, 'create'])
    ->name('orders.create');

// Cookies
Route::post('/cookies/accept', function () {
    session(['cookies_accepted' => true]);
    return response()->json(['success' => true]);
})->name('cookies.accept');




require __DIR__ . '/auth.php';
