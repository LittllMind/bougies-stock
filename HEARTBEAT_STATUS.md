# Heartbeat Status - 2026-03-25 20:54

## 🫀 Vérification Heartbeat

**Date:** 2026-03-25 20:54:42  
**Branche:** master  
**Statut:** ⚠️ Tests en échec

---

## 📊 Tests - Vue d'ensemble

| Métrique | Valeur |
|----------|--------|
| Tests Bougie | 6/6 OK (100%) |
| Tests DetailBougie | 3/5 OK (60%) |
| Tests globaux | Partiellement en échec |

---

## ❌ Problèmes Critiques Identifiés

### 1. Tables Legacy Manquantes
**Erreur:** `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'bougies_stock_test.vinyles' doesn't exist`

- **Source:** `DashboardController` ligne 34 
- **Impact:** Multiple tests Stats échouent
- **Cause:** Le `DashboardController` et `ChartController` utilisent encore le modèle `Vinyle` qui n'existe plus dans les bases de données de test

### 2. DashboardController - Ligne 34
```php
// DashboardController.php - ligne 34
$stockValue = Vinyle::selectRaw('SUM(quantite * prix) as valeur')->value('valeur') ?? 0;
```

**Controller entiers impactés:**
- `Admin\DashboardController::index`
- `Admin\DashboardController::chartsApi` (ligne 136)

### 3. Tests DetailBougie - Erreur 500
**Erreur:** Expected 200, received 500
- `/catalogue/{id}` retourne 500 lors des tests
- Probablement lié à `@vite` en mode test ou problème de layout
- À investiguer plus en profondeur

---

## ✅ Corrections Appliquées

### 1. `CatalogueController` - Ligne 39
**Problème:** Utilisait `$bougie->image_url` alors que l'attribut n'était pas dans la réponse JSON

**Correction:** Suppression de `image_url` du mapping JSON dans `index()`

```php
// AVANT
'bougiesJson' => $bougies->map(fn($b) => [
    // ...
    'image_url' => $bougie->image_url,
])

// APRÈS
'bougiesJson' => $bougies->map(fn($b) => [
    // ... (sans image_url)
])
```

### 2. `DetailBougieTest` - Tests avec stock
**Problème:** Les tests créaient des bougies sans stock (quantite=0), mais le `CatalogueController::show()` retourne 404 si quantite <= 0

**Correction:** Utilisation de `stockOk()` dans les tests:
```php
// AVANT
$bougie = Bougie::factory()->create([...])

// APRÈS
$bougie = Bougie::factory()->stockOk()->create([...])
```

---

## 📋 Fichiers Modifiés

| Fichier | Modification | Statut |
|---------|--------------|--------|
| `app/Http/Controllers/CatalogueController.php` | Suppression `image_url` | ✅ Fait |
| `tests/Feature/DetailBougieTest.php` | Ajout `stockOk()` | ✅ Fait |

---

## 🔧 Actions Requises

### Priorité Haute:
1. **Corriger DashboardController**
   - Remplacer références à `Vinyle` par `Bougie`
   - Mettre à jour calculs de stock/valeur
   - Adapter les API charts

2. **Corriger ChartController (si utilisé)**
   - Même approche: remplacer legacy par bougies

### Priorité Moyenne:
3. **Investiger erreur 500 DetailBougie**
   - Vérifier layout `app.blade.php` avec `@vite`
   - Tester en mode local avec `php artisan serve`

---

## 📁 Git Status

```
 M app/Http/Controllers/BougieController.php
 M app/Http/Controllers/CatalogueController.php
 M resources/views/catalogue/show.blade.php
 M routes/web.php
 M tests/Feature/User/RolePermissionsTest.php
```

---

## 📝 Notes

- La fonctionnalité catalogue fonctionne en local
- Les tests d'intégration échouent sur des dépendances legacy
- Nécessite une passe de nettoyage des controllers legacy

---
*Rapport généré par Heartbeat - 2026-03-25*


---

## 2026-03-26 00:24 — Heartbeat Check

### 🩺 Diagnostic automatique

**État Git:**
- Branche: `master`
- Commits divergents: 31 local / 6 remote (divergence importante)
- Fichiers non commités: 26 (tests, vues, contrôleurs API catalogue)

**État Tests:**
- Configuration BDD tests: MySQL `bougies_stock_test`
- ⚠️ Erreur DB: Table 'migrations' already exists (conflit RefreshDatabase)
- Tests Bougie: 🔴 Échec (problème infrastructure test)

**Tâche en cours identifiée:**
- **T4.1 VueJS Catalogue Client** — Fichiers créés mais non commités
- API Catalogue JSON: `CatalogueApiController.php` ✅
- Page Vue.js: `catalogue/vue.blade.php` ✅
- Tests: `CatalogueApiTest.php`, `CatalogueVueTest.php` 🔄

### 🔧 Actions prioritaires

1. **Corriger config tests** — Isoler les tests avec transactions propres
2. **Valider tests Catalogue** — Vérifier T4.1 fonctionnel
3. **Créer branche + commit** — Si tests verts

### 📝 Fichiers prêts à committer (26):
```
A  app/Http/Controllers/CatalogueApiController.php
A  resources/views/catalogue/vue.blade.php
A  resources/views/layouts/navigation-front.blade.php
M  app/Http/Controllers/CatalogueController.php
M  app/Models/Bougie.php
M  database/factories/BougieFactory.php
M  routes/web.php
A  tests/Feature/CatalogueApiTest.php
M  tests/Feature/CatalogueTest.php
A  tests/Feature/CatalogueVueTest.php
...
```
