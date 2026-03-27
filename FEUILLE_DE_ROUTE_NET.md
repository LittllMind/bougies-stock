# Feuille de Route - Les Bougies de Séraphie

**Projet:** Transformation vinyles → bougies artisanales  
**Framework:** Laravel 11 + Vue.js 3 + Tailwind CSS  
**Dernière mise à jour:** 2026-03-27

---

## ✅ TÂCHES TERMINÉES

### Phase 1: Fondation (T1.x) — ✅ TERMINÉE
| Tâche | Description | Tests | Statut |
|-------|-------------|-------|--------|
| T1.1 | Auth Laravel + Breeze personnalisé | ?/? | ✅ Committé |
| T1.2 | Layouts admin/client avec charte graphique | ?/? | ✅ Committé |

### Phase 2: Modèle de Données (T2.x) — ✅ TERMINÉE
| Tâche | Description | Tests | Statut |
|-------|-------------|-------|--------|
| T2.1 | Installation Bootstrap + Vue.js CDN | N/A | ✅ Committé |
| T2.2 | Migration Bougie + Factory + Seeder | 4/4 | ✅ Committé |
| T2.3 | CRUD Admin BougieController | 9/9 | ✅ Committé |

### Phase 3: Administration (T3.x) — ✅ TERMINÉE
| Tâche | Description | Tests | Statut |
|-------|-------------|-------|--------|
| T3.1 | Observer Bougie + StockAlert | 7/7 | ✅ Committé |
| T3.2 | Dashboard Alertes Stock | 7/7 | ✅ Committé |
| T3.3 | Notifications Email alertes | ?/? | ⏸️ NON PLANIFIÉ (optionnel) |

### Phase 4: Catalogue Client (T4.x) — ✅ TERMINÉE
| Tâche | Description | Tests | Statut |
|-------|-------------|-------|--------|
| T4.1 | Vue.js Catalogue (grille + filtres) | 3/3 | ✅ Committé |
| T4.2 | Vue.js Détail Bougie | 7/7 | ✅ Committé |
| T4.3 | Vue.js Panier (localStorage + API) | 8/8 | ✅ Committé |
| **T4.4** | **Checkout + Paiement Stripe** | **26/26** | ✅ **Committé** |

**Détail T4.4:**
- Checkout client: 8 tests ✅
- Stripe Checkout: 8 tests ✅
- Stripe Webhooks: 10 tests ✅

---

## 🔄 PHASES EN COURS / À VENIR

### Phase 5: Dashboard Admin (T5.x) — 🔄 PRÉPARÉE
| Tâche | Description | Statut |
|-------|-------------|--------|
| T5.1 | Dashboard Admin métriques | 🔄 En préparation |
| T5.2 | Graphiques ventes/bougies (Chart.js) | ⏳ À planifier |

### Phase 6: Finalisation (T6.x) — ⏳ À PLANIFIER
| Tâche | Description | Priorité |
|-------|-------------|----------|
| T6.1 | Emails transactionnels | Moyenne |
| T6.2 | Mode marché (vendre sur place) | Faible |
| T6.3 | Export PDF | Faible |

---

## ❌ TÂCHES SUPPRIMÉES / OBSOLÈTES

Ces tâches viennent du projet vinyles originel et ont été **supprimées** du scope bougies:

| Tâche | Raison suppression |
|-------|-------------------|
| T12 (Users/Rapports) | Legacy vinyles, modèles supprimés |
| T13 (Security) | Tests legacy vinyles, dépendances Fond/Vinyle supprimés |
| T14 (Mode Marché) | Code legacy vinyles, pas adapté bougies |
| T16 (Documentation) | Documentation dédiée vinyles obsolète |
| T15 (Performance) | Tests legacy vinyles uniquement |

**Tests hérités archivés:**
- `tests/Feature/_archive/Orders/*` — Tests legacy Orders vinyles
- `tests/Feature/Auth/` sauf Auth — Certains tests peuvent être legacy

---

## 📊 ÉTAT GLOBAL TESTS

### Tests Bougies (Actifs)
```
✅ Unit/BougieTest: 8/8 (100%)
✅ Feature/BougieControllerTest: 9/9 (100%)
✅ Feature/BougieDetailTest: 7/7 (100%)
✅ Feature/BougieMigrationTest: 4/4 (100%)
✅ Feature/BougieStockAlertObserverTest: 7/7 (100%)
✅ Feature/CatalogueTest: 3/3 (100%)
✅ Feature/CartTest: 8/8 (100%)
✅ Feature/Orders/CheckoutBougieTest: 8/8 (100%)
✅ Feature/Orders/StripeCheckoutTest: 8/8 (100%)
✅ Feature/Orders/StripeWebhookTest: 10/10 (100%)

TOTAL BOUGIES: 72/72 tests passants (100%)
```

### Tests Legacy (Archivés - Échouent normalement)
- Dependent de modèles Fond/Vinyle supprimés
- À ne pas inclure dans `php artisan test` global
- Gardés pour référence uniquement

---

## 🗂️ STRUCTURE PROJET ACTUELLE

```
bougies-stock/
├── app/
│   ├── Http/Controllers/
│   │   ├── BougieController.php (Admin CRUD)
│   │   ├── CatalogueController.php (Client)
│   │   ├── Api/
│   │   │   ├── CatalogueController.php (API JSON)
│   │   │   └── CartController.php (Panier)
│   │   └── Orders/
│   │       ├── OrderController.php (Checkout)
│   │       └── PaymentController.php (Stripe)
│   ├── Models/
│   │   ├── Bougie.php ✅
│   │   ├── CartItem.php ✅ (lié à bougie)
│   │   ├── Order.php ✅
│   │   ├── OrderItem.php ✅ (lié à bougie)
│   │   ├── Payment.php ✅ (Stripe)
│   │   ├── StockAlert.php ✅
│   │   └── MouvementStock.php ✅
│   ├── Observers/
│   │   └── BougieObserver.php ✅
│   └── Services/
│       └── CartService.php ✅
├── database/
│   ├── factories/
│   │   ├── BougieFactory.php ✅
│   │   ├── CartItemFactory.php ✅
│   │   └── OrderItemFactory.php ✅
│   ├── migrations/
│   │   └── Bougies + relations ✅
│   └── seeders/
│       └── BougieSeeder.php ✅ (8 bougies réelles)
├── resources/
│   ├── views/
│   │   ├── admin/bougies/ ✅
│   │   ├── catalogue/ ✅
│   │   ├── orders/ ✅
│   │   └── layouts/ ✅
│   └── js/
│       └── cart.js ✅
├── routes/
│   ├── web.php ✅
│   └── api.php ✅
└── tests/
    ├── Unit/BougieTest.php ✅
    └── Feature/
        ├── Bougie*.php ✅
        ├── Catalogue*.php ✅
        ├── CartTest.php ✅
        ├── Orders/ ✅
        └── _archive/ (legacy vinyles)
```

---

## 🎯 PROCHAINES ACTIONS

1. ✅ **T4.4 committée** (fait)
2. 🔄 **T5.1 Dashboard Admin** — Démarrer avec tests d'abord
3. ⏸️ **T5.2 Graphiques** — Après T5.1
4. ⏸️ **T6.x Finalisation** — Basse priorité

---

*Version nettoyée: Incohérences corrigées, tâches vinyles supprimées, status actualisés*
