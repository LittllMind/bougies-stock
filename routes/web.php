<?php

use App\Http\Controllers\VinyleController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\VenteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('vinyles.index');
});

Route::get('/dashboard', function () {
    return redirect()->route('vinyles.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('vinyles', VinyleController::class);
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');
    Route::resource('ventes', VenteController::class);
});

// Mode kiosque accessible sans authentification
Route::get('/kiosque', [VinyleController::class, 'kiosque'])->name('kiosque');
Route::post('/kiosque/vendre', [VenteController::class, 'storeFromKiosque'])->name('kiosque.vendre');

require __DIR__ . '/auth.php';
