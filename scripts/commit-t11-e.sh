#!/bin/bash
# Commit T11-E: Tests Integration Commandes (Order Flow)

cd "$(dirname "$0")/.."

echo "🔧 Préparation commit T11-E..."

# Ajouter les fichiers de tests
git add tests/Feature/Orders/
git add scripts/commit-t11-e.sh

# Commit
git commit -m "test/T11-E: Tests Integration Commandes - Flow complet

- OrderControllerIntegrationTest (16 tests)
  * Accès formulaire commande (guest/auth)
  * Validation champs obligatoires
  * Création commande avec livraison
  * Adresse facturation différente
  * Page paiement et création commande
  * Réutilisation commande existante
  * Mes commandes (pagination)
  * Check stock intégration
  * Commande avec fond sélectionné
  * Flow complet guest
  * Flow complet authentifié

Coverage: OrderController, CartService, flow E2E"

echo ""
git log --oneline -3
echo ""
echo "✅ T11-E commité !"