#!/bin/bash
# 🏃 Commit Marathon Phase 2.1 Dashboard
# Commit des 5 tâches créées/modifiées

cd "$(dirname "$0")"

echo "🏃 Marathon Phase 2.1 - Commit des 5 tâches"
echo "============================================"

# Vérifier l'état git
echo "📊 État du dépôt :"
git status --short | head -20

echo ""
echo "📝 Récapitulatif des modifications :"
echo "-----------------------------------"
echo "T1: Fix lien Panier → /cart"
echo "   - resources/views/layouts/kiosque.blade.php"
echo ""
echo "T2: 'Mes commandes' client"
echo "   - app/Http/Controllers/OrderController.php"
echo "   - resources/views/orders/my-orders.blade.php"
echo "   - routes/web.php"
echo ""
echo "T3: Dashboard avec Stock Vinyles"
echo "   - resources/views/dashboard.blade.php (créé/modifié)"
echo ""
echo "T4: Gestion Stock Fonds"
echo "   - resources/views/fonds/index.blade.php"
echo ""
echo "T5: Statistiques Admin"
echo "   - resources/views/stats.blade.php"
echo ""

# Demande confirmation
read -p "Continuer le commit ? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "📝 Ajout des fichiers..."
    
    # Ajout des fichiers créés/modifiés
    git add resources/views/layouts/kiosque.blade.php
    git add app/Http/Controllers/OrderController.php
    git add resources/views/orders/my-orders.blade.php
    git add routes/web.php 2>/dev/null || true
    git add resources/views/dashboard.blade.php
    git add resources/views/fonds/index.blade.php 2>/dev/null || true
    git add resources/views/stats.blade.php
    
    echo "💾 Création du commit..."
    git commit -m "feat: Phase 2.1 Dashboard complet - 5/5 tâches

🎯 Réalisations:
- T1: Fix lien Panier /panier → /cart (route('cart.index'))
- T2: 'Mes commandes' client avec historique et statuts
- T3: Dashboard unifié violet/rose avec sections selon rôles
- T4: Gestion Stock Fonds modernisée (miroir/doré)
- T5: Statistiques Admin avec KPIs, top ventes, marges

🦞 Identité visuelle:
- Thème violet/rose unifié sur toutes les vues admin
- Gradients, icônes, cartes interactives
- Responsive mobile-first"
    
    echo ""
    echo "✅ Commit effectué !"
    echo "Hash: $(git rev-parse --short HEAD 2>/dev/null || echo 'N/A')"
    echo ""
    git log --oneline -3
    
    # Mise à jour des fichiers de suivi
    echo ""
    echo "📝 Mise à jour du suivi..."
    date '+%Y-%m-%d %H:%M' > .last-commit.txt
    echo "$(git rev-parse --short HEAD)" >> .last-commit.txt
    
else
    echo "❌ Commit annulé"
    exit 0
fi
