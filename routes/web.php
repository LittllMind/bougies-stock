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




// Temporary debug route for local testing of cart merge (remove after use)
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Vinyle;
use App\Models\User;

Route::get('/_debug/merge-cart-test', function () {
    if (!app()->environment('local')) {
        abort(404);
    }

    $source = request()->query('source', 'tst-session-xyz');

    // Create anonymous cart placeholder
    Cart::where('session_id', $source)->whereNull('user_id')->delete();
    $anon = Cart::create(['session_id' => $source, 'expires_at' => now()->addHours(2)]);

    $vin = Vinyle::where('quantite', '>', 0)->first();
    if (!$vin) {
        return response('NO_VIN', 500);
    }

    $anon->items()->create(['vinyle_id' => $vin->id, 'fond_id' => null, 'quantite' => 1, 'prix_unitaire' => $vin->prix]);

    $user = User::first();
    if (!$user) {
        return response('NO_USER', 500);
    }

    Auth::loginUsingId($user->id);

    $before = app(App\Services\CartService::class)->count();
    app(App\Services\CartService::class)->mergeAnonymousCart($source);
    $after = app(App\Services\CartService::class)->count();

    return response()->json([ 'source' => $source, 'anon_cart_id' => $anon->id, 'user_id' => $user->id, 'before' => $before, 'after' => $after ]);
});

require __DIR__ . '/auth.php';
