# Les Bougies de Séraphie 🕯️

> Boutique artisanale de bougies — kiosque en ligne, vente sur stand et back-office de gestion.

[![CI](https://github.com/LittllMind/bougies-stock/actions/workflows/ci.yml/badge.svg)](https://github.com/LittllMind/bougies-stock/actions/workflows/ci.yml)
[![Deploy](https://github.com/LittllMind/bougies-stock/actions/workflows/deploy.yml/badge.svg)](https://github.com/LittllMind/bougies-stock/actions/workflows/deploy.yml)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20)](https://laravel.com)
[![Stripe](https://img.shields.io/badge/Stripe-Ready-635BFF)](https://stripe.com)

## 🎯 À propos

Application Laravel pour **Les Bougies de Séraphie**. Elle gère un catalogue de bougies artisanales avec :

- **Kiosque web** — catalogue public, panier, commande en ligne
- **Mode marché** — caisse emport pour ventes physiques sur stand
- **Tunnel Stripe** — checkout, webhooks et confirmations
- **Back-office** — stock, commandes, utilisateurs et rapports PDF

Site de production : [https://les-bougies-de-seraphie.fr](https://les-bougies-de-seraphie.fr)

## 🚀 Fonctionnalités

- [x] **Kiosque web** — grille de bougies avec fiches détaillées
- [x] **Panier & checkout Stripe** — paiement en ligne sécurisé
- [x] **Mode marché** — vente physique, fond musical, rapport journalier
- [x] **Gestion de stock** — mouvements, alertes seuil, historique
- [x] **Rôles utilisateurs** — admin, employé, client
- [x] **Rapports PDF** — ventes, stock, inventaire, alertes
- [x] **Sauvegardes** — Spatie Laravel Backup

## 🛠️ Stack Technique

| Couche | Technologie |
|--------|-------------|
| Backend | PHP 8.3, Laravel 10 |
| Frontend | Vue 3, Vite 5, Tailwind CSS, AlpineJS, Bootstrap 5.3 |
| Base de données | SQLite (local), MySQL 8.0 (production) |
| Auth | Laravel Breeze, Sanctum |
| Paiement | Stripe |
| Médias | Spatie Laravel Media Library |
| Backup | Spatie Laravel Backup |
| Tests | PHPUnit 10, Playwright |
| CI/CD | GitHub Actions |

## 📚 Documentation

| Document | Description |
|----------|-------------|
| [📐 Architecture](docs/ARCHITECTURE.md) | Structure, flux de données, rôles |
| [🚀 Déploiement](docs/DEPLOYMENT.md) | Mise en prod Nginx + SSL |
| [📡 API](docs/API.md) | Endpoints JSON |
| [💳 Stripe](docs/STRIPE_INSTALL.md) | Configuration des paiements |
| [✅ Post-déploiement](docs/POST-DEPLOYMENT.md) | Checklist après mise en prod |
| [🔧 Troubleshooting](docs/TROUBLESHOOTING.md) | Erreurs courantes |
| [🗺️ Adresses](docs/ADRESSES.md) | Gestion des adresses clients |

## 🧪 Tests

```bash
# Tests PHP
php artisan test

# Tests e2e
npm run test:e2e

# Avec couverture
php artisan test --coverage
```

## 📋 Installation locale

### Prérequis

- PHP 8.3+
- Composer 2.x
- Node.js 20+
- MySQL 8.0+ ou SQLite

### Étapes

```bash
# 1. Cloner
git clone https://github.com/LittllMind/bougies-stock.git
cd bougies-stock

# 2. Dépendances
composer install
npm ci

# 3. Configuration
cp .env.example .env
php artisan key:generate

# 4. Variables d'environnement
# APP_NAME="Bougies de Seraphie"
# APP_URL=http://127.0.0.1:8003
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
# STRIPE_KEY=sk_test_...
# STRIPE_SECRET=sk_test_...
# STRIPE_WEBHOOK_SECRET=whsec_...

# 5. Base de données
php artisan migrate --seed

# 6. Build
npm run build

# 7. Démarrer
php artisan serve --port=8003
```

## 🖥️ Utilisation

| Page | URL locale | Accès |
|------|------------|-------|
| Kiosque | `http://localhost:8003/kiosque` | Public |
| Panier | `http://localhost:8003/cart` | Public |
| Admin | `http://localhost:8003/admin` | Admin / Employé |
| Mode marché | `http://localhost:8003/admin/marche` | Admin / Employé |
| Compte client | `http://localhost:8003/client/dashboard` | Client |

## 📦 Structure du projet

```
bougies-stock/
├── app/
│   ├── Http/Controllers/   # Web + API
│   ├── Models/             # Bougie, Order, Vente, User...
│   ├── Services/           # Logique métier (PDF, Stripe...)
│   └── Policies/           # Autorisations RBAC
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/              # Blade
│   └── js/                 # Vue / JS frontal
├── routes/
├── tests/                  # Feature + Unit + e2e
└── docs/                   # Documentation métier
```

## 🤝 Contribuer

1. Créer une branche : `git checkout -b feature/nom`
2. Tests verts : `php artisan test`
3. Commit conventionnel : `git commit -m "feat: ..."`
4. Pousser et ouvrir une PR

## 📄 Licence

MIT — voir [LICENSE](LICENSE)

## 📞 Contact

- **Projet** : Les Bougies de Séraphie
- **Site** : [https://les-bougies-de-seraphie.fr](https://les-bougies-de-seraphie.fr)
- **Repository** : [LittllMind/bougies-stock](https://github.com/LittllMind/bougies-stock)

---

*Dernière mise à jour : octobre 2026*
