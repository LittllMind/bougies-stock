#!/bin/bash
# 🏃 Commit Tâche 5 - Section Statistiques (Admin only)
# Phase 2.1 Dashboard - Marathon Nocturne

cd ~/aurelien/vinyles-stock

echo "📦 Commit Tâche 5 : Section Statistiques"

# Ajout des fichiers
git add resources/views/stats.blade.php

# Commit
git commit -m "feat: Statistiques Admin - dashboard moderne violet/rose avec KPIs et top ventes

- Vue complète modernisée avec filtre de période (30j/3m/12m/all)
- Cartes interactives : CA total, CA période, ventes, panier moyen
- Alertes stock cliquables : stock bas et ruptures
- KPIs marges : marge brute, taux marge, marge potentielle
- Top 10 ventes avec barres de progression
- Identité visuelle violet/rose unifiée avec gradients"

echo "✅ Commit Tâche 5 effectué"
echo "Hash: $(git rev-parse --short HEAD)"
