#!/bin/bash
# 🏃 MARATHON - Commit #3: Dashboard Stock Vinyles

cd /home/aur-lien/.picoclaw/workspace/vinyles-stock

echo "📝 Commit Tâche #3: Dashboard Stock Vinyles..."

# Ajouter le dashboard complet avec accès Stock Vinyles
git add resources/views/dashboard.blade.php

# Commit avec message descriptif
git commit -m "feat: Dashboard avec accès Stock Vinyles (Admin/Employé)

- Dashboard complet avec sections selon rôle (Client/Employé/Admin)
- Section Espace Client: catalogue, panier, mes commandes, adresses
- Section Gestion Stock (Admin/Employé): accès Vinyles, Fonds, Ventes
- Section Administration (Admin): accès Statistiques
- Thème violet/rose unifié cohérent avec le kiosque
- Cartes cliquables avec hover effects et transitions"

echo "✅ Commit #3 créé avec succès !"
git log --oneline -3
