#!/bin/bash

# Script de veille concurrentielle hebdomadaire pour Les Bougies de Séraphie
# Ce script est exécuté automatiquement tous les lundis à 9h

RAPPORT_DATE=$(date +%Y-%m-%d)
RAPPORT_DIR="/home/aur-lien/.picoclaw/workspace/rapports-veille"
RAPPORT_FILE="$RAPPORT_DIR/veille-${RAPPORT_DATE}.md"

# Création du répertoire si inexistant
mkdir -p "$RAPPORT_DIR"

# Template du rapport
cat > "$RAPPORT_FILE" << EOF
# 📊 Rapport de Veille Concurrentielle - Les Bougies de Séraphie

**Date:** $RAPPORT_DATE  
**Prochain rapport:** $(date -d "+7 days" +%Y-%m-%d)

---

## 🎯 Concurrents Suivis

### Luxe Français Historique
- **Cire Trudon** (95€-130€)
- **Diptyque** (58€-75€)
- **Goutal** (55€-70€)

### Artisanat Français Contemporain
- **Kerzon** (38€-48€)
- **La Belle Mèche** (42€-55€)

### Digitaux Natives
- **Boy Smells** (45€-65€)
- **Otherland** (36€-48€)

---

## 💰 Analyse Prix

| Notre Collection | Prix | Benchmark | Écart |
|------------------|------|-----------|-------|
| Spirit | 45€ | Diptyque 58€ | -22% ✅ |
| Art | 28€ | - | Compétitif |
| Nature | 12-18€ | Kerzon 38€ | -65% ✅ |

---

## 🎨 Tendances Design à Surveiller

- [ ] Couleurs (tendances Pantone)
- [ ] Typographies populaires
- [ ] Patterns UX/UI
- [ ] Packaging innovants

---

## 📈 Opportunités Identifiées

*À compléter après analyse des concurrents.*

---

*Rapport généré automatiquement - Veille hebdomadaire Séraphie*
EOF

echo "✅ Rapport de veille créé: $RAPPORT_FILE"
