#!/bin/bash
# Commit Tâche 2 : Mes Commandes Client

cd /home/aur-lien/.picoclaw/workspace/vinyles-stock

echo "📦 Commit Tâche 2: Mes commandes (Client)"
echo "=========================================="

git add app/Http/Controllers/OrderController.php \
        resources/views/orders/my-orders.blade.php \
        routes/web.php \
        resources/views/layouts/app.blade.php \
        resources/views/layouts/kiosque.blade.php

echo "📋 Fichiers ajoutés :"
git status --short

echo ""
echo "💾 Commit en cours..."
git commit -m 'feat: "Mes commandes" - historique client avec statuts et détails'

echo ""
echo "✅ Commit réalisé :"
git log --oneline -1