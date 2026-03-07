<?php

use App\Http\Controllers\VinyleController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\FondController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PaymentController;

// ============================================
// ROUTES PUBLIQUES (Accès sans authentification)
// ============================================
Route::get('/', [HomeController::class, 'landing'])->name('landing');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::get('/dashboard', function () {
    return redirect()->route('kiosque.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// ============================================
// ROUTES ADMIN (Accès restreint: admin ET employe)
// ============================================
Route::middleware(['auth', 'role:admin,employe'])->group(function () {
    // Gestion complète des vinyles (CRUD)
    Route::resource('vinyles', VinyleController::class);

    // Statistiques
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');

    // Gestion des fonds
    Route::resource('fonds', FondController::class)->only(['index', 'update']);

    // Gestion des ventes (admin)
    Route::resource('ventes', VenteController::class);
});

// ============================================
// ROUTES KIOSQUE (Accès public pour consultation)
// ============================================
Route::prefix('kiosque')->name('kiosque.')->group(function () {
    // Consultation du catalogue - accessible à tous (visiteurs inclus)
    Route::get('/', [VinyleController::class, 'kiosque'])->name('index');

    // Achat - nécessite d'être connecté
    Route::post('/vendre', [VenteController::class, 'storeFromKiosque'])
        ->middleware('auth')
        ->name('vendre');
});

// ============================================
// ROUTES CLIENT (Accès public ou authentifié)
// ============================================
// Panier public (accessible sans connexion)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/{item}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Création de commande (authentifié)
Route::middleware('auth')->group(function () {
    // Adresses
    Route::resource('addresses', AddressController::class);
    Route::post('/addresses/{id}/set-default', [AddressController::class, 'setDefault'])->name('addresses.setDefault');
    
    // Commandes
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/payment', [OrderController::class, 'payment'])->name('orders.payment');
    
    // Routes de succès/annulation de commande
    Route::get('/orders/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/orders/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Cookies
Route::post('/cookies/accept', function () {
    session(['cookies_accepted' => true]);
    return response()->json(['success' => true]);
})->name('cookies.accept');


// ===========================================
// ROUTES STRIPE
//============================================

// Routes de paiement Stripe
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Webhook Stripe (doit être public)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');



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
    $merged = app(App\Services\CartService::class)->mergeAnonymousCart($source, $anon->id);
    $after = app(App\Services\CartService::class)->count();

    return response()->json([ 'source' => $source, 'anon_cart_id' => $anon->id, 'user_id' => $user->id, 'before' => $before, 'after' => $after, 'merged' => $merged ]);
});

require __DIR__ . '/auth.php';
