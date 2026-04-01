<?php

use App\Http\Controllers\BougieController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\CatalogueApiController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ModeMarcheController;

// ============================================
// ROUTES PUBLIQUES (Accès sans authentification)
// ============================================
Route::get('/', [HomeController::class, 'landing'])->name('landing');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Page checkout publique
Route::get('/checkout', function () {
    return view('orders.checkout');
})->name('checkout');

// Page confirmation commande
Route::get('/confirmation/{reference}', function ($reference) {
    return view('orders.confirmation', ['reference' => $reference]);
})->name('confirmation');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// ============================================
// ROUTES ADMIN ORDERS (Admin et Employé)
// ============================================
Route::middleware(['auth', 'role:admin,employe'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderAdminController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderAdminController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderAdminController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Admin\OrderAdminController::class, 'cancel'])->name('orders.cancel');
});

// ============================================
// ROUTES ADMIN BOUGIES (Admin et Employé)
// ============================================
Route::middleware(['auth', 'role:admin,employe'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('bougies', BougieController::class)->parameters([
        'bougies' => 'bougie'
    ]);
    Route::patch('/bougies/{bougie}/stock', [BougieController::class, 'updateStock'])->name('bougies.updateStock');
});

// ============================================
// ROUTES BOUGIES - Emp/Admin
// ============================================
Route::middleware(['auth', 'role:admin,employe'])->group(function () {
    Route::get('/bougies', [BougieController::class, 'index'])->name('bougies.index');
});

// ============================================
// ROUTES API CATALOGUE PUBLIC (JSON pour Vue.js)
// ============================================
Route::get('/api/catalogue/bougies', [CatalogueApiController::class, 'index'])->name('api.catalogue.index');
Route::get('/api/catalogue/bougies/{reference}', [CatalogueApiController::class, 'show'])->name('api.catalogue.show');

// Route catalogue index publique - Redirection vers /kiosque pour URL unique
Route::get('/catalogue', function () {
    return redirect()->route('kiosque', request()->all());
})->name('catalogue');

Route::get('/kiosque', [CatalogueController::class, 'index'])->name('kiosque');
Route::get('/catalogue/{reference}', [CatalogueController::class, 'show'])->name('catalogue.show');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
});

// ============================================
// ROUTES ADMIN REPORTS (Admin et Employé)
// ============================================
Route::middleware(['auth', 'role:admin,employe'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reports/monthly', [\App\Http\Controllers\Admin\ReportController::class, 'monthlyReportForm'])->name('reports.monthly');
    Route::post('/reports/monthly', [\App\Http\Controllers\Admin\ReportController::class, 'generateMonthlyReport'])->name('reports.monthly.generate');
    
    // Rapports T13.1
    Route::get('/reports/stock', [\App\Http\Controllers\Admin\ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/artists', [\App\Http\Controllers\Admin\ReportController::class, 'artists'])->name('reports.artists');
    
    // T5.2 - Rapports PDF Inventaire et Financier
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/inventory/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'inventoryPDF'])->name('reports.inventory.pdf');
    Route::get('/reports/financial/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'financialPDF'])->name('reports.financial.pdf');
    Route::get('/reports/alerts/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'alertsPDF'])->name('reports.alerts.pdf');
    
});

// ============================================
// ROUTES ADMIN DASHBOARD (Admin et Employé)
// ============================================
Route::middleware(['auth', 'role:admin,employe'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [\App\Http\Controllers\Admin\DashboardController::class, 'statsApi'])->name('stats.json');
    Route::get('/stats/charts', [\App\Http\Controllers\Admin\DashboardController::class, 'chartsApi'])->name('stats.charts');
});

// ============================================
// ROUTES STATISTIQUES LEGACY (redirect vers admin dashboard)
// ============================================
Route::middleware(['auth', 'role:admin,employe'])->get('/stats', function () {
    return redirect()->route('admin.dashboard');
})->name('stats');

// ============================================
// ROUTES MODE MARCHÉ (Admin et Employé)
// ============================================
// Définies en dehors du groupe admin pour garder les noms 'marche.xxx'
Route::middleware(['auth', 'role:admin,employe'])->prefix('admin/marche')->name('marche.')->group(function () {
    Route::get('/', [ModeMarcheController::class, 'index'])->name('index');
    Route::post('/store', [ModeMarcheController::class, 'store'])->name('store');
    Route::get('/ventes-jour', [ModeMarcheController::class, 'ventesJour'])->name('ventes-jour');
    Route::get('/check-stock/{bougie}', [ModeMarcheController::class, 'checkStock'])->name('check-stock');
    Route::post('/{order}/cancel', [ModeMarcheController::class, 'cancel'])->name('cancel');
    Route::get('/export', [ModeMarcheController::class, 'export'])->name('export');
});

// ============================================
// ROUTES ALERTES STOCK (Admin et Employé)
// ============================================
Route::middleware(['auth', 'role:admin,employe'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/stock-alerts', [\App\Http\Controllers\Admin\StockAlertController::class, 'index'])->name('stock-alerts.index');
    Route::get('/stock-alerts/{stockAlert}', [\App\Http\Controllers\Admin\StockAlertController::class, 'show'])->name('stock-alerts.show');
    Route::patch('/stock-alerts/{stockAlert}/resolve', [\App\Http\Controllers\Admin\StockAlertController::class, 'resolve'])->name('stock-alerts.resolve');
    Route::delete('/stock-alerts/{stockAlert}', [\App\Http\Controllers\Admin\StockAlertController::class, 'destroy'])->name('stock-alerts.destroy');
});

// ============================================
// ROUTES CLIENT (Accès public ou authentifié)
// ============================================
// Panier public (accessible sans connexion)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/add', [CartController::class, 'addFromCatalogue'])->name('add');
    Route::post('/add', [CartController::class, 'addFromCatalogue']);
    Route::patch('/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/{item}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Création de commande (authentifié)
Route::middleware('auth')->group(function () {
    // Dashboard client
    Route::get('/client/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');

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

    // Mes commandes (historique client)
    Route::get('/mes-commandes', [OrderController::class, 'myOrders'])->name('orders.my');

// Profil utilisateur
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profil utilisateur (legacy - gardés pour compatibilité) 
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update'); 
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password');
});

// API Synchronisation panier localStorage → DB
Route::post('/api/cart/sync', [CartController::class, 'sync'])->name('cart.sync');

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
use App\Services\CartService;

Route::get('/_debug/merge-cart-test', function () {
    if (!app()->environment('local')) {
        abort(404);
    }

    $source = request()->query('source', 'tst-session-xyz');

    // Create anonymous cart placeholder
    \App\Models\Cart::where('session_id', $source)->whereNull('user_id')->delete();
    $anon = \App\Models\Cart::create(['session_id' => $source, 'expires_at' => now()->addHours(2)]);

    $bougie = \App\Models\Bougie::where('quantite', '>', 0)->first();
    if (!$bougie) {
        return response('NO_BOUGIE', 500);
    }

    $anon->items()->create(['bougie_id' => $bougie->id, 'quantite' => 1, 'prix_unitaire' => $bougie->prix]);

    $user = \App\Models\User::first();
    if (!$user) {
        return response('NO_USER', 500);
    }

    Auth::loginUsingId($user->id);

    $cartService = app(CartService::class);
    $before = $cartService->count();
    $merged = $cartService->mergeAnonymousCart($source, $anon->id);
    $after = $cartService->count();

    return response()->json([ 
        'source' => $source, 
        'anon_cart_id' => $anon->id, 
        'user_id' => $user->id, 
        'before' => $before, 
        'after' => $after, 
        'merged' => $merged 
    ]);
});

/*
|--------------------------------------------------------------------------
| Routes Publiques - Catalogue Client Vue.js (anciennes routes, maintenant redirigées)
|--------------------------------------------------------------------------
*/

// DEBUG: Quick check
Route::get('/_debug/bougies', [DebugController::class, 'bougies']);
Route::post('/_debug/seed', [DebugController::class, 'seedTestBougies']);

require __DIR__ . '/auth.php';
<<<<<<< HEAD
=======


// Routes Paolo Admin
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/calendar', [\App\Http\Controllers\Admin\DashboardController::class, 'calendar'])->name('admin.calendar');
});

// ============================================
// ROUTES ADMIN LIEUX (T1.2 + T1.3)
// ============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/lieux', [\App\Http\Controllers\Admin\LieuController::class, 'index'])->name('lieux.index');
    Route::get('/lieux/create', [\App\Http\Controllers\Admin\LieuController::class, 'create'])->name('lieux.create');
    Route::post('/lieux', [\App\Http\Controllers\Admin\LieuController::class, 'store'])->name('lieux.store');
});
>>>>>>> origin/feature/T1.2-lieu-crud
