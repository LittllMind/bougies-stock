# 🔍 Analyse de l'audit - Vinyles Stock

Date: 14 février 2026

---

## 📊 RÉSUMÉ EXÉCUTIF

### ✅ Points positifs
- Architecture Laravel standard bien structurée
- Système de panier implémenté (Cart, CartItem)
- Authentification via Laravel Breeze
- Gestion des médias avec Spatie Media Library
- Tests configurés (PHPUnit)
- Système d'alertes de stock

### ⚠️ Points d'attention majeurs
1. **Pas de gestion d'adresses** (tables addresses manquantes)
2. **Pas de middleware Admin dédié** (protection routes admin à vérifier)
3. **Duplication Cart/Order** (deux systèmes similaires ?)
4. **Système de rôles basique** (champ `role` ajouté mais middleware absent)
5. **Pas de contrôleur Admin** dédié

---

## 🛒 ANALYSE DU PANIER

### ✅ Ce qui est bien implémenté

**Modèles:**
- `Cart` : Panier principal lié à un utilisateur
- `CartItem` : Articles du panier avec relations vers Vinyle et Fond
- Relations Eloquent correctement définies

**Contrôleur:**
- `CartController` présent
- Routes dédiées : `/cart/*`

**Fonctionnalités (routes détectées):**
```php
Route::get('/', [CartController::class, 'index'])          // Voir panier
Route::post('/add', [CartController::class, 'add'])        // Ajouter article
Route::patch('/{item}', [CartController::class, 'update']) // Modifier quantité
Route::delete('/{item}', [CartController::class, 'remove'])// Supprimer article
Route::post('/clear', [CartController::class, 'clear'])    // Vider panier
Route::get('/count', [CartController::class, 'count'])     // Compteur articles
```

**Middleware spécifique:**
- `MergeCartOnLogin` : Fusion panier session → panier utilisateur à la connexion ✅

**Vue:**
- `resources/views/cart/index.blade.php` ✅

### ⚠️ Points à vérifier dans le code

1. **Validation des prix côté serveur**
   ```bash
   # À vérifier dans CartController.php
   grep -A 10 "public function add" app/Http/Controllers/CartController.php
   ```
   
2. **Gestion du stock**
   - Le panier vérifie-t-il le stock avant ajout ?
   - Que se passe-t-il si le stock change entre l'ajout et le checkout ?

3. **Sécurité**
   - CSRF protection sur toutes les routes ? ✅ (Laravel par défaut)
   - Un utilisateur peut-il modifier le panier d'un autre ?

4. **Performance**
   - Eager loading des relations ? (éviter N+1)

### 🔧 Actions recommandées

**PRIORITÉ HAUTE:**
- [ ] Vérifier la validation des prix dans `CartController::add()`
- [ ] Vérifier la vérification du stock avant ajout
- [ ] Tester la sécurité : un user peut-il accéder au panier d'un autre ?

**PRIORITÉ MOYENNE:**
- [ ] Ajouter des tests unitaires pour le panier
- [ ] Vérifier l'eager loading dans `CartController`

---

## 📍 ANALYSE DES ADRESSES

### ❌ PROBLÈME MAJEUR : Système d'adresses absent

**Constat:**
- Aucun modèle `Address` détecté
- Aucune migration `addresses`
- Aucun contrôleur `AddressController`

**Impact:**
- Comment les commandes sont-elles livrées ?
- Où sont stockées les adresses de livraison/facturation ?

### 🔍 À investiguer

```bash
# Vérifier si les adresses sont dans la table orders
grep -r "delivery_address\|shipping_address\|billing_address" database/migrations/
grep -r "delivery_address\|shipping_address\|billing_address" app/Models/Order.php
```

### ✅ Solutions recommandées

**Option 1 : Adresses intégrées dans Order (simple, pour MVP)**
```php
// Table orders
$table->string('shipping_first_name');
$table->string('shipping_last_name');
$table->string('shipping_address');
$table->string('shipping_city');
$table->string('shipping_postal_code');
$table->string('shipping_phone');
// Idem pour billing si différent
```

**Option 2 : Table addresses séparée (recommandé, réutilisable)**
```php
// Nouvelle migration
Schema::create('addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->enum('type', ['shipping', 'billing']);
    $table->string('first_name');
    $table->string('last_name');
    $table->string('address_line_1');
    $table->string('address_line_2')->nullable();
    $table->string('city');
    $table->string('postal_code');
    $table->string('country')->default('FR');
    $table->string('phone');
    $table->boolean('is_default')->default(false);
    $table->timestamps();
});
```

### 🔧 Actions recommandées

**PRIORITÉ CRITIQUE:**
- [ ] **Vérifier comment sont gérées les adresses actuellement**
- [ ] **Décider de l'approche** (intégrée vs table séparée)
- [ ] **Créer la migration `addresses`**
- [ ] **Créer le modèle `Address`**
- [ ] **Créer `AddressController` avec CRUD**
- [ ] **Ajouter les routes**
- [ ] **Créer les vues de gestion d'adresses**
- [ ] **Intégrer au processus de checkout**

---

## 🔐 ANALYSE DE L'AUTHENTIFICATION

### ✅ Ce qui est bien implémenté

**Package utilisé:**
- Laravel Breeze ✅ (bon choix, simple et efficace)

**Contrôleurs Auth détectés:**
- `AuthenticatedSessionController` (login/logout)
- `RegisteredUserController` (inscription)
- `PasswordController` (changement mot de passe)
- `PasswordResetLinkController` (demande reset)
- `NewPasswordController` (nouveau mot de passe)
- `EmailVerificationPromptController`
- `EmailVerificationNotificationController`
- `VerifyEmailController`
- `ConfirmablePasswordController`

**Fonctionnalités complètes:**
- ✅ Inscription
- ✅ Connexion
- ✅ Déconnexion
- ✅ Mot de passe oublié
- ✅ Vérification email
- ✅ Confirmation mot de passe pour actions sensibles

**Configuration:**
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

### ⚠️ Points à vérifier

1. **Email verification obligatoire ?**
   ```bash
   # Vérifier dans routes/web.php
   grep "verified" routes/web.php
   ```

2. **Rate limiting sur login ?**
   ```bash
   # Vérifier dans AuthenticatedSessionController
   grep "throttle\|RateLimiter" app/Http/Controllers/Auth/AuthenticatedSessionController.php
   ```

3. **Session configuration**
   ```bash
   # Vérifier dans .env
   grep "SESSION_" .env
   ```

### 🔧 Actions recommandées

**PRIORITÉ MOYENNE:**
- [ ] Vérifier que `verified` middleware est bien utilisé sur routes sensibles
- [ ] Confirmer le rate limiting sur login
- [ ] Vérifier la configuration session pour production

---

## 👤 ANALYSE DES STATUTS (CLIENT / ADMIN)

### ⚠️ PROBLÈME : Système de rôles incomplet

**Ce qui existe:**
- Migration `add_role_to_users_table` ✅
- Middleware `CheckUserRole` ✅

**Ce qui manque:**
- ❌ Pas de middleware Admin dédié trouvé
- ❌ Pas de contrôleur Admin dédié
- ❌ Dashboard admin pas clairement identifié

### 🔍 À investiguer

```bash
# 1. Vérifier le champ role dans User
cat app/Models/User.php | grep -A 5 "role"

# 2. Vérifier CheckUserRole middleware
cat app/Http/Middleware/CheckUserRole.php

# 3. Vérifier les routes protégées
grep -r "CheckUserRole\|role" routes/web.php

# 4. Vérifier la migration role
cat database/migrations/*add_role_to_users_table.php
```

### 📋 Questions à répondre

1. **Type de système de rôles ?**
   - Simple booléen `is_admin` ?
   - Enum `role` (admin, client, vendeur...) ?
   - RBAC complet avec permissions ?

2. **Comment sont protégées les routes admin ?**
   - Middleware appliqué sur groupes de routes ?
   - Vérification dans les contrôleurs ?

3. **Fonctionnalités admin existantes ?**
   - Gestion vinyles (CRUD)
   - Gestion ventes
   - Gestion utilisateurs ?
   - Statistiques

### ✅ Recommandations d'implémentation

**Approche simple (recommandée pour ce projet):**

```php
// Migration
$table->enum('role', ['client', 'admin'])->default('client');

// Model User
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

// Middleware Admin
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }
        return $next($request);
    }
}

// Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::resource('users', UserController::class);
    // ...
});
```

### 🔧 Actions recommandées

**PRIORITÉ HAUTE:**
- [ ] **Vérifier le contenu de la migration `add_role_to_users_table`**
- [ ] **Examiner `CheckUserRole` middleware**
- [ ] **Identifier les routes qui devraient être admin uniquement**
- [ ] **Créer un vrai middleware Admin si nécessaire**
- [ ] **Créer un seeder pour créer un compte admin**

**PRIORITÉ MOYENNE:**
- [ ] Ajouter des policies pour autorisations granulaires
- [ ] Créer un dashboard admin dédié
- [ ] Tests pour vérifier qu'un client ne peut pas accéder à l'admin

---

## 🔄 ANALYSE CART vs ORDER

### 🤔 Duplication potentielle détectée

**Modèles similaires:**
- `Cart` + `CartItem`
- `Order` + `OrderItem`

**Questions:**
1. Quelle est la différence entre Cart et Order ?
2. Le panier devient-il une commande au checkout ?
3. Pourquoi deux systèmes distincts ?

### 🔍 À investiguer

```bash
# 1. Vérifier Order model
cat app/Models/Order.php

# 2. Vérifier OrderItem model
cat app/Models/OrderItem.php

# 3. Vérifier OrderController
find app/Http/Controllers -name "*Order*"

# 4. Vérifier les migrations
cat database/migrations/*create_orders_table.php
cat database/migrations/*create_order_items_table.php
```

### ✅ Architecture recommandée

**Workflow normal:**
```
Cart (panier actif)
  ↓ [Checkout validé]
Order (commande confirmée)
  ↓ [Paiement]
Order (statut: payée)
  ↓ [Expédition]
Order (statut: expédiée)
```

**Si c'est le cas, c'est bien ! ✅**

**Si Order n'est pas utilisé, c'est du code mort à supprimer ⚠️**

---

## 📊 AUTRES OBSERVATIONS

### ✅ Points positifs supplémentaires

1. **Gestion de stock sophistiquée**
   - Table `stock_alerts` ✅
   - Email d'alerte de stock critique ✅
   - Champ `seuil_alerte` sur vinyles ✅

2. **Système de ventes**
   - Modèles `Vente` + `LigneVente`
   - Contrôleur et vues
   - Route kiosque pour vente rapide

3. **Scripts de déploiement**
   - `deploy.sh`
   - `push_deploy_prod.sh`
   - Documentation `hostinger-prod.md`

4. **Médias**
   - Spatie Media Library intégré
   - Conversions d'images

5. **Statistiques**
   - Route `/stats`
   - Contrôleur `StatsController`

### ⚠️ Points d'attention

1. **Fichiers de déploiement multiples**
   - `deploy.sh`
   - `push_deploy_prod.sh`
   - `commit-add.sh`
   - Risque de confusion, à unifier ?

2. **Pas de tests**
   - Dossier `tests/` existe mais vide ?
   ```bash
   find tests/ -name "*.php" | wc -l
   ```

3. **Documentation**
   - README.md existe mais contenu ?
   ```bash
   cat README.md
   ```

---

## 🎯 PLAN D'ACTION PRIORISÉ

### 🔴 PRIORITÉ CRITIQUE (À FAIRE IMMÉDIATEMENT)

1. **Système d'adresses**
   - [ ] Vérifier comment sont stockées les adresses actuellement
   - [ ] Créer table `addresses` si nécessaire
   - [ ] Implémenter CRUD adresses
   - [ ] Intégrer au checkout

2. **Système de rôles / Admin**
   - [ ] Examiner la migration `add_role_to_users_table`
   - [ ] Vérifier `CheckUserRole` middleware
   - [ ] Créer middleware Admin dédié si nécessaire
   - [ ] Sécuriser toutes les routes admin

### 🟡 PRIORITÉ HAUTE (Cette semaine)

3. **Sécurité du panier**
   - [ ] Vérifier validation des prix
   - [ ] Vérifier vérification du stock
   - [ ] Tester isolation utilisateurs

4. **Clarifier Cart vs Order**
   - [ ] Documenter la différence
   - [ ] Supprimer si code mort
   - [ ] Implémenter workflow complet si manquant

5. **Tests**
   - [ ] Ajouter tests unitaires panier
   - [ ] Ajouter tests autorisation (admin vs client)
   - [ ] Tests feature pour checkout

### 🟢 PRIORITÉ MOYENNE (Ce mois)

6. **Nettoyage**
   - [ ] Unifier scripts de déploiement
   - [ ] Supprimer code mort
   - [ ] Améliorer documentation

7. **Performance**
   - [ ] Vérifier eager loading
   - [ ] Ajouter index base de données
   - [ ] Cache queries lourdes

8. **UX**
   - [ ] Améliorer messages d'erreur
   - [ ] Ajouter validations frontend
   - [ ] Optimiser responsive

---

## 📝 COMMANDES POUR INVESTIGATION APPROFONDIE

```bash
# 1. Examiner les modèles clés
cat app/Models/User.php
cat app/Models/Cart.php
cat app/Models/Order.php
cat app/Models/Address.php  # Si existe

# 2. Examiner les contrôleurs
cat app/Http/Controllers/CartController.php
cat app/Http/Controllers/OrderController.php  # Si existe

# 3. Examiner les middleware
cat app/Http/Middleware/CheckUserRole.php
cat app/Http/Middleware/MergeCartOnLogin.php

# 4. Examiner les migrations
cat database/migrations/*add_role_to_users_table.php
cat database/migrations/*create_orders_table.php
cat database/migrations/*create_carts_table.php

# 5. Vérifier les routes protégées
cat routes/web.php

# 6. Vérifier les tests
ls -la tests/Feature/
ls -la tests/Unit/

# 7. Vérifier .env
cat .env | grep -v "PASSWORD\|KEY"
```

---

## 📊 SCORE DE QUALITÉ

| Catégorie | Note | Commentaire |
|-----------|------|-------------|
| 🛒 Panier | 7/10 | Bien implémenté mais à vérifier côté sécurité |
| 📍 Adresses | 0/10 | **Système manquant - CRITIQUE** |
| 🔐 Auth | 9/10 | Laravel Breeze bien configuré |
| 👤 Rôles | 4/10 | Structure présente mais incomplète |
| 🏗️ Architecture | 8/10 | Structure Laravel standard propre |
| 🔒 Sécurité | ?/10 | À évaluer après investigation |
| 📝 Tests | 0/10 | Apparemment absents |
| 📖 Documentation | 6/10 | Scripts et docs présents |

**Note globale : 6/10** (en attente de corrections critiques)

---

## 🚀 PROCHAINES ÉTAPES

1. **Lire ce rapport en détail**
2. **Exécuter les commandes d'investigation** (ci-dessus)
3. **Remplir la checklist** `AUDIT_CHECKLIST.md`
4. **Créer une branche** `feature/addresses-system`
5. **Implémenter les corrections critiques**
6. **Tester en local**
7. **Déployer sur staging** (si disponible)
8. **Déployer en production**

---

Date: 14 février 2026
Auditeur: Assistant Claude
Projet: Vinyles Stock