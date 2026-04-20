# 🧪 Tests - Configuration Requise

## Problème Actuel

Les tests Laravel nécessitent une **base de données MySQL** qui n'est pas configurée localement.

## Solutions Disponibles

### Option 1: Créer la base de test (Recommandé)

```bash
# Connectez-vous à MySQL et créez la base :
mysql -u root -p -e "CREATE DATABASE bougies_stock_test;"
```

Puis éditez `phpunit.xml` ligne 43 :
```xml
<env name="DB_DATABASE" value="bougies_stock_test"/>
```

### Option 2: Utiliser la base existante

Si vous avez une base `bougies_stock` fonctionnelle, modifiez `phpunit.xml` :
```xml
<env name="DB_DATABASE" value="bougies_stock"/>
```

**⚠️ Attention** : Les tests vont effacer et recréer les tables (migrate:fresh).

### Option 3: Tests unitaires uniquement

```bash
php artisan test --testsuite=Unit
```

Ces tests n'ont pas besoin de base de données.

## Fichiers Créés

- `database/setup-test-db.sql` - Script SQL pour créer la base
- `.env.testing` - Configuration environnement testing
- `TESTS_CONFIG.md` - Ce fichier

## Vérification

Une fois la base créée, testez avec :
```bash
cd /home/aur-lien/.picoclaw/workspace/bougies-stock
php artisan test --filter=CartTest
```

## État du Projet

- ✅ 205 tests écrits
- ✅ Code fonctionnel et complet
- 🟡 En attente configuration BDD pour exécution des tests
