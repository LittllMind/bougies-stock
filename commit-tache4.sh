#!/bin/bash
# 🏃 MARATHON - Commit #4: Gestion Stock Fonds

cd /home/aur-lien/.picoclaw/workspace/vinyles-stock

echo "📝 Commit Tâche #4: Gestion Stock Fonds..."

# Ajouter la vue modernisée des fonds
git add resources/views/fonds/index.blade.php

# Commit avec message descriptif
git commit -m "feat: Gestion Stock Fonds - vue moderne violet/rose avec alertes et totaux

- Vue complètement modernisée avec thème violet/rose
- Tableau avec icônes animées et gradients par type (miroir, doré...)
- Edition inline des quantités avec validation
- Alertes visuelles: rupture (rouge), stock bas (jaune), OK (vert)
- Calcul valeur totale du stock en temps réel
- Interface responsive et intuitive"

echo "✅ Commit #4 créé avec succès !"
git log --oneline -3
