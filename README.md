# Vinyl Stock 🎸

> Plateforme de vente de vinyles hydrodécoupés

[![Laravel](https://img.shields.io/badge/Laravel-11.x-brightgreen)](https://laravel.com)
[![Stripe](https://img.shields.io/badge/Stripe-Test-green)](https://stripe.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue)](https://php.net)

## 🎯 À propos

Vinyl Stock est une plateforme e-commerce pour la vente de vinyles hydrodécoupés. Le projet utilise **Laravel 11** pour le backend, **Tailwind CSS** pour le frontend, et **Stripe** pour les paiements en ligne.

**Localisation** : 48150, Le rozier, France

## 🚀 Fonctionnalités

### Phase 1 ✅ (Terminée)

- ✅ **Kiosque de consultation** - Page publique avec grille de vinyles
- ✅ **Tunnel de vente complet** - Panier, adresses, commande
- ✅ **Paiement Stripe** - Checkout, webhook, confirmation
- ✅ **RBAC** - Système de rôles (Admin/Employé/Client)

### Phase 2 (En cours)

- 📦 Gestion de stock (CRUD produits, quantités, historique)
- 📊 Dashboard avec statistiques et graphiques
- 🎁 Fonctionnalités avancées (réservation, fidélité, emails)
- 🌐 Déploiement en production

## 🛠️ Stack Technique

| Composant | Technologie |
|-----------|-------------|
| Framework | Laravel 11 |
| Base de données | MySQL |
| CSS | Tailwind CSS |
| Paiement | Stripe (Test/Production) |
| Authentification | Laravel Breeze |
| RBAC | Middleware personnalisé |

## 📋 Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | admin@example.com | password |
| Employé | employe@example.com | password |
| Client | client@example.com | password |

> **Note** : Pour des tests en production, changer les mots de passe et utiliser des clés API Stripe réelles.

## 🚀 Installation

### Prérequis

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Installation

```bash
# 1. Cloner le projet
git clone <repository-url> vinyl-stock
cd vinyl-stock

# 2. Installer les dépendances PHP
composer install

# 3. Copier le fichier de configuration
cp .env.example .env

# 4. Configurer les variables d'environnement
# - APP_NAME="Vinyl Stock"
# - APP_URL="http://localhost:8000"
# - DB_DATABASE="vinyl_stock"
# - DB_USERNAME="root"
# - DB_PASSWORD="votre_mot_de_passe"
# - STRIPE_KEY="sk_test_..."
# - STRIPE_SECRET="sk_test_..."
# - STRIPE_WEBHOOK_SECRET="whsec_..."

# 5. Installer les dépendances Node
npm install

# 6. Compiler les assets
npm run build

# 7. Installer les migrations et seeds
php artisan migrate --seed

# 8. Lancer le serveur
php artisan serve
```

## 📖 Documentation

- [Documentation Stripe](docs/STRIPE_INSTALL.md)
- [Guide de test Stripe](docs/STRIPE_TEST.md)
- [Système d'adresses](docs/ADRESSES.md)
- [Guide RBAC](SECURITE_ROLES.md)
- [Comptes de test](COMPTES_TEST.md)

## 🧪 Tests

### Tests Stripe

```bash
# Lancer le serveur
php artisan serve

# Ouvrir http://localhost:8000/kiosque
# Ajouter des produits au panier
# Passer une commande avec Stripe (mode test)
```

### Tests RBAC

Se connecter avec les comptes de test pour vérifier les permissions.

## 📦 Structure du projet

```
vinyl-stock/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CartController.php
│   │   │   ├── PaymentController.php
│   │   │   └── AddressController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Cart.php
│   │   ├── Order.php
│   │   ├── Payment.php
│   │   └── Address.php
│   └── Services/
│       └── CartService.php
├── resources/
│   ├── views/
│   │   ├── kiosque.blade.php
│   │   ├── cart.blade.php
│   │   ├── checkout.blade.php
│   │   └── success.blade.php
│   └── css/
│       └── app.css
├── routes/
│   └── web.php
├── database/
│   ├── migrations/
│   └── seeds/
├── docs/
│   ├── STRIPE_INSTALL.md
│   ├── STRIPE_TEST.md
│   └── ADRESSES.md
└── scripts/
    └── stripe-webhook.sh
```

## 🎨 Identité visuelle

- **Couleurs** : Violet → Rose
- **Mode** : Dark mode par défaut
- **Style** : Moderne, épuré, responsive

## 📊 Métriques

- **Temps de chargement** : < 2s
- **Taux de conversion** : > 3% (objectif)
- **Uptime** : 99.9% (objectif production)

## 🤝 Contribuer

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

MIT License - Voir [LICENSE](LICENSE) pour plus de détails

## 📞 Contact

- **Projet** : Vinyl Stock
- **Localisation** : 48150, Le rozier
- **Développeur** : Aurélien

---

**Made with ❤️ using Laravel**
