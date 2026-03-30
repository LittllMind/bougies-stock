# 🛒 Roadmap Tunnel de Vente — Les Bougies de Séraphie

**Objectif:** Livrer un tunnel de vente complet et testé, du catalogue jusqu'à la confirmation de paiement Stripe.

---

## 📊 ÉTAT ACTUEL — Diagnostic Rapide

| Étape | Statut | Tests | Problèmes connus |
|-------|--------|-------|------------------|
| **1. Kiosque** (Catalogue) | 🟡 À vérifier | ? | Vue.js fonctionnel ? |
| **2. Panier** | 🟡 À vérifier | ? | DB cart vs session ? |
| **3. Checkout** (Adresse) | 🟡 À vérifier | 8/8 ? | Formulaire adresse ? |
| **4. Paiement** (Stripe) | 🟡 À vérifier | 8/8 ? | Webhook config ? |
| **5. Confirmation** | 🟡 À vérifier | ? | Email envoyé ? |

**Météo globale:** 🟡 Inconnu — Tests à relancer après nettoyage DB

---

## 🎯 PHASES DE DÉVELOPPEMENT

### PHASE 1 — Validation & Stabilisation (Jour 1)
**Objectif:** Confirmer ce qui marche, identifier les blocages

- [ ] **P1.1** Relancer tests checkout complets
- [ ] **P1.2** Vérifier fonctionnement Kiosque Vue.js
- [ ] **P1.3** Vérifier panier (ajout/suppression/quantités)
- [ ] **P1.4** Tester checkout manuellement (parcou
- [ ] **P1.5** Config Stripe webhook (si manquant)

**Livrable:** Rapport détaillé de l'état actuel avec liste des défauts

---

### PHASE 2 — Corrections Tunnel (Jour 1-2)
**Objectif:** Réparer les éléments cassés identifiés en P1

- [ ] **P2.1** Corriger erreurs tests CheckoutBougieTest
- [ ] **P2.2** Corriger erreurs tests StripeCheckoutTest
- [ ] **P2.3** Corriger erreurs tests StripeWebhookTest
- [ ] **P2.4** Corriger erreurs tests OrderConfirmationEmailTest
- [ ] **P2.5** Stabiliser Kiosque (filtres, affichage, responsive)
- [ ] **P2.6** Stabiliser Panier (persistance, calculs, UI)

**Livrable:** Tous les tests du tunnel passent (vert)

---

### PHASE 3 — Intégration Stripe Production (Jour 2)
**Objectif:** Paiement réel fonctionnel

- [ ] **P3.1** Vérifier clés Stripe (.env)
- [ ] **P3.2** Configurer webhook Stripe Dashboard → /stripe/webhook
- [ ] **P3.3** Tester paiement test (4242 4242 4242 4242)
- [ ] **P3.4** Vérifier décrémentation stock après paiement
- [ ] **P3.5** Vérifier création commande en base
- [ ] **P3.6** Vérifier email confirmation envoyé

**Livrable:** Paiement test réussi end-to-end

---

### PHASE 4 — Polish UX & Responsive (Jour 3)
**Objectif:** Expérience utilisateur fluide

- [ ] **P4.1** Loader sur transitions (panier → checkout → paiement)
- [ ] **P4.2** Messages d'erreur clairs (carte refusée, stock insuffisant)
- [ ] **P4.3** Page succès avec récapitulatif commande
- [ ] **P4.4** Navigation mobile optimisée
- [ ] **P4.5** Transitions Vue.js fluides

**Livrable:** Tunnel responsive et user-friendly

---

### PHASE 5 — Tests E2E & Livraison (Jour 3-4)
**Objectif:** Validation complète avant mise en production

- [ ] **P5.1** Scénario test: Client achète 1 bougie
- [ ] **P5.2** Scénario test: Client achète plusieurs articles
- [ ] **P5.3** Scénario test: Stock insuffisant → message approprié
- [ ] **P5.4** Scénario test: Paiement refusé → retour panier
- [ ] **P5.5** Scénario test: Abandon panier → récupération possible
- [ ] **P5.6** Tests de charge ( Lighthouse performance )

**Livrable:** Tunnel de vente production-ready

---

## 🔗 Flux Utilisateur Cible

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Landing    │────▶│   Kiosque   │────▶│   Panier    │────▶│   Checkout  │────▶│   Paiement  │
│   (SEO)     │     │ (Vue.js 🕯) │     │(Vue.js 🛒)  │     │ (Adresse 📬)│     │ (Stripe 💳) │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
                                                                                        │
                                                                                        ▼
                                                                              ┌─────────────┐
                                                                              │ Confirmation│
                                                                              │   (Email)   │
                                                                              └─────────────┘
```

---

## 📋 Checkpoints Critiques

| # | Checkpoint | Validation |
|---|------------|------------|
| 1 | Kiosque chargé | Liste bougies affichée, filtres fonctionnels |
| 2 | Panier persistant | Ajout → refresh → toujours là |
| 3 | Checkout adresse | Validation champs, choix adresse existante |
| 4 | Stripe Session | Redirection checkout.stripe.com OK |
| 5 | Webhook reçu | `stripe listen` ou Dashboard webhook test |
| 6 | Commande créée | DB: orders + order_items + payment |
| 7 | Stock décrémenté | bougie.quantite -= order_item.quantite |
| 8 | Email envoyé | Mailtrap/reel reçu avec récap |

---

## 🚨 Risques & Mitigations

| Risque | Probabilité | Impact | Mitigation |
|--------|-------------|--------|------------|
| Webhook Stripe non reçu | Moyenne | 🔴 Critique | Endpoint public, retry logic, polling backup |
| Stock oversold | Faible | 🔴 Critique | Verrou DB, checkstock avant paiement |
| Panier perdu auth | Moyenne | 🟡 Élevé | Merge cart anonyme → user à la connexion |
| UX mobile casse | Moyenne | 🟡 Élevé | Tests sur vrai device, Tailwind responsive |

---

## 📁 Fichiers Clés à Surveiller

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── CatalogueController.php      # Kiosque
│   │   ├── CartController.php           # Panier
│   │   ├── OrderController.php          # Checkout/Adresse
│   │   └── PaymentController.php        # Stripe
├── Services/
│   ├── CartService.php                  # Logique panier
│   └── SessionCartService.php           # Panier invité
├── Observers/
│   └── OrderObserver.php                  # Email confirmation

resources/
├── views/
│   ├── kiosque.blade.php                # Vue.js catalogue
│   ├── cart/
│   │   └── index.blade.php              # Panier Vue.js
│   └── orders/
│       ├── create.blade.php             # Checkout adresse
│       ├── payment.blade.php            # Récap Stripe
│       └── confirmation.blade.php       # Page succès
└── js/
    ├── kiosque.js                       # Vue.js catalogue
    └── cart.js                          # Vue.js panier

tests/
└── Feature/
    └── Orders/
        ├── CheckoutBougieTest.php       # Tests checkout
        ├── StripeCheckoutTest.php       # Tests Stripe session
        ├── StripeWebhookTest.php        # Tests webhook
        └── OrderConfirmationEmailTest.php # Tests email
```

---

## ⏱️ Estimation Totale

| Phase | Durée estimée |
|-------|---------------|
| P1 — Validation | 2-3h |
| P2 — Corrections | 4-6h |
| P3 — Stripe Prod | 2-3h |
| P4 — Polish UX | 3-4h |
| P5 — Tests E2E | 2-3h |
| **TOTAL** | **13-19h** (~2-3 jours) |

---

**Météo départ:** 🟡 Jaune — Tunnel existant mais tests cassés (problème DB)
**Prochaine action:** Relancer tests sur DB propre → diagnostic précis

---
*Créé: 2026-03-30*
*Priorité: CRITIQUE — Tunnel de vente = cœur du business*
