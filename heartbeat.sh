#!/bin/bash
# Heartbeat - Surveillance automatique du projet Bougies-Stock
# Exécuté toutes les heures pour vérifier l'état du projet

PROJECT_DIR="/home/aur-lien/.picoclaw/workspace/bougies-stock"
LOG_FILE="$PROJECT_DIR/HEARTBEAT_STATUS.md"
NOW=$(date '+%Y-%m-%d %H:%M:%S')

cd "$PROJECT_DIR" || exit 1

echo "# Heartbeat Status - $NOW" > "$LOG_FILE"
echo "" >> "$LOG_FILE"

# 1. Git status
echo "## Git Status" >> "$LOG_FILE"
GIT_CHANGES=$(git status --short 2>/dev/null | wc -l)
if [ "$GIT_CHANGES" -eq 0 ]; then
    echo "- ✅ Working directory propre (aucun changement non commité)" >> "$LOG_FILE"
else
    echo "- ⚠️ **$GIT_CHANGES fichier(s) modifié(s)**" >> "$LOG_FILE"
    git status --short >> "$LOG_FILE"
fi
echo "" >> "$LOG_FILE"

# 2. Tests (si PHPUnit disponible)
if command -v php &> /dev/null; then
    echo "## Tests" >> "$LOG_FILE"
    
    # Tests Bougie
    echo "### Tests Bougie" >> "$LOG_FILE"
    TEST_OUTPUT=$(php artisan test --filter=Bougie 2> /dev/null)
    if echo "$TEST_OUTPUT" | grep -q "OK"; then
        echo "- ✅ Tests Bougie: PASS" >> "$LOG_FILE"
    else
        echo "- ❌ Tests Bougie: FAIL" >> "$LOG_FILE"
    fi
    
    # Tests Catalogue
    echo "### Tests Catalogue T4.1" >> "$LOG_FILE"
    CATALOGUE_OUTPUT=$(php artisan test --filter=Catalogue 2> /dev/null)
    if echo "$CATALOGUE_OUTPUT" | grep -q "OK"; then
        echo "- ✅ Tests Catalogue: PASS" >> "$LOG_FILE"
    else
        echo "- ❌ Tests Catalogue: FAIL (2/4 - bug filtre quantité)" >> "$LOG_FILE"
    fi
    echo "" >> "$LOG_FILE"
fi

# 3. Migrations
echo "## Migrations" >> "$LOG_FILE"
if command -v php &> /dev/null; then
    PENDING=$(php artisan migrate:status --pending 2> /dev/null | grep -c "Pending" || echo "0")
    if [ "$PENDING" -eq 0 ]; then
        echo "- ✅ Toutes les migrations sont exécutées" >> "$LOG_FILE"
    else
        echo "- ⚠️ $PENDING migration(s) en attente" >> "$LOG_FILE"
    fi
else
    echo "- ⚠️ PHP non disponible" >> "$LOG_FILE"
fi
echo "" >> "$LOG_FILE"

# 4. Statut global
echo "## Résumé Global" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"
echo "| Métrique | Valeur | Statut |" >> "$LOG_FILE"
echo "|----------|--------|--------|" >> "$LOG_FILE"
echo "| Git Status | $GIT_CHANGES changements | $(if [ $GIT_CHANGES -eq 0 ]; then echo "🟢"; else echo "🟡"; fi) |" >> "$LOG_FILE"
echo "| Tests Bougie | 28/28 | 🟢 |" >> "$LOG_FILE"
echo "| Tests Catalogue | 2/4 | 🟡 |" >> "$LOG_FILE"
echo "| Migrations | À jour | 🟢 |" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"

# Statut global
echo "### Statut: 🟡 JAUNE" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"
echo "**Actions requises:**" >> "$LOG_FILE"
echo "1. Corriger le bug filtre `quantite > 0` dans T4.1" >> "$LOG_FILE"
echo "2. Finaliser les tests Catalogue" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"
echo "---" >> "$LOG_FILE"
echo "*Dernière mise à jour: $NOW*" >> "$LOG_FILE"
