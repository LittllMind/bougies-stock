# 🎉 T6.3 — Profil Client

**Description:** Espace client personnel avec navigation unifiée, historique commandes détaillé et gestion adresses.  
**Estimation:** 2-3h  
**Priorité:** Basse  
**Date début:** 2026-03-31

## 📊 ÉTAT ACTUEL

L'espace client existe partiellement mais est fragmenté :
- ✅ Profil edit (`/profile/edit`) - Laravel Breeze basique
- ✅ Mot de passe - Formulaire Breeze
- ✅ Adresses (`/addresses/*`) - Controller complet mais views basiques
- ✅ Mes commandes (`/mes-commandes`) - View existante mais très basique
- ❌ Navigation unifiée style Séraphie
- ❌ Dashboard client centralisé
- ❌ Tests spécifiques

## 🎯 OBJECTIFS T6.3

Créer un **espace client harmonisé** avec navigation latérale style Séraphie:

### Structure visuelle:
```
+------------------+------------------------+
|  🕯️ Séraphie     |      Header            |
|  Dashboard       +------------------------+
|                  |  Contenu dynamique     |
|  📊 Vue d'ensemble|                       |
|  📦 Mes commandes |                       |
|  📍 Mes adresses  |                       |
|  ⚙️ Mon profil    |                       |
|                  +------------------------+
```

## 📝 SOUS-TÂCHES

### 1. Dashboard Client Vue (~30min)
- [ ] Tests: Dashboard `/client/dashboard` accessible
- [ ] Tests: Affiche stats commandes
- [ ] Tests: Affiche dernière commande
- [ ] Route: `GET /client/dashboard`
- [ ] Controller: `ClientDashboardController@index`
- [ ] View: `resources/views/client/dashboard.blade.php`

### 2. Navigation Latérale (~20min)
- [ ] Tests: Navigation visible sur toutes les pages client
- [ ] Tests: Liens actifs selon la page
- [ ] Component: `resources/views/client/partials/sidebar.blade.php`
- [ ] Layout: `resources/views/layouts/client.blade.php` étendant app

### 3. Style Séraphie sur profil (~20min)
- [ ] Tests: Page profil accessible avec nouveau layout
- [ ] Refactor: `resources/views/profile/edit.blade.php` → hériter de layouts/client
- [ ] Harmonisation couleurs (amber/orange/crème)
- [ ] Composants cartes style Séraphie

### 4. Améliorer "Mes Commandes" (~30min)
- [ ] Tests: Liste commandes paginée
- [ ] Tests: Détails commande cliquable
- [ ] Refactor: `views/orders/my-orders.blade.php` → intégrer dans layout client
- [ ] Vue: `views/orders/show.blade.php` détail commande client
- [ ] Badge statut coloré (pending, paid, shipped, delivered)

### 5. Intégrer Adresses (~20min)
- [ ] Tests: Liste adresses dans sidebar
- [ ] Tests: CRUD adresses fonctionnel
- [ ] Refactor: `views/addresses/*` → intégrer dans layout client
- [ ] Suppression sécurisée (empêche si adresse utilisée dans commande)

### 6. Statistiques Client (~20min)
- [ ] Tests: Stats calculées correctement
- [ ] Nombre commandes total
- [ ] Dépenses totales
- [ ] Points fidélité (placeholder pour futures)
- [ ] Bougie préférée (plus achetée)

### 7. Tests Fonctionnels (~10min)
- [ ] Tests route accessibles auth
- [ ] Tests 403 si non auth
- [ ] Tests données affichées correctes
- [ ] Tests navigation entre pages

## 🛠️ ARCHITECTURE

### Routes (`routes/web.php`):
```php
Route::middleware('auth')->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'showOrder'])->name('orders.show');
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});
```

### Nouveaux fichiers:
```
app/
  Http/
    Controllers/
      ClientDashboardController.php

resources/
  views/
    layouts/
      client.blade.php       # Layout avec sidebar
    client/
      dashboard.blade.php    # Vue d'ensemble
      partials/
        sidebar.blade.php    # Navigation latérale
        stats.blade.php      # Cartes stats
      orders/
        index.blade.php      # Liste commandes (refactor my-orders)
        show.blade.php       # Détail commande
      addresses/
        index.blade.php      # Liste adresses (depuis addresses/index)
      profile/
        edit.blade.php       # Refactor profile/edit

tests/
  Feature/
    Client/
      ClientDashboardTest.php
      ClientProfileTest.php
      ClientOrdersTest.php
      ClientAddressesTest.php
```

### Couleurs Séraphie:
- Background principal: `bg-amber-50` (crème)
- Sidebar: `bg-white border-r-2 border-amber-200`
- Accent actif: `bg-amber-100 text-amber-900 border-r-4 border-amber-500`
- Header: Gradient `from-amber-600 to-orange-500`
- Texte: `text-amber-900` / `text-gray-700`

## ✅ CRITÈRES D'ACCEPTATION

1. [ ] Navigation latérale présente sur toutes les pages client
2. [ ] Page dashboard affiche stats et dernière commande
3. [ ] Historique commandes paginé avec badges statut colorés
4. [ ] Détail commande accessible depuis liste
5. [ ] Adresses gérables dans l'espace client
6. [ ] Profil éditable avec style Séraphie
7. [ ] Tests 6-8 fonctionnent (100% vert)

## 📊 ESTIMATION

| Étape | Durée |
|-------|-------|
| 1. Dashboard | 30min |
| 2. Navigation | 20min |
| 3. Profil style | 20min |
| 4. Orders | 30min |
| 5. Adresses | 20min |
| 6. Tests | 10min |
| **TOTAL** | **~2h30** |
