#!/bin/bash
cd /home/aur-lien/.picoclaw/workspace/bougies-stock

# 📊 Tests Bougie
TEST_OUTPUT=$(php artisan test --testsuite=Bougie 2>&1)
TESTS_PASS=$(echo "$TEST_OUTPUT" | grep -oP '\d+(?= passed)' || echo "0")
TESTS_TOTAL=$(echo "$TEST_OUTPUT" | grep -oP '\d+(?= generated)' || echo "0")
TEST_STATUS=$(if [ "$TESTS_PASS" = "$TESTS_TOTAL" ] && [ "$TESTS_TOTAL" -gt 0 ]; then echo "✅"; elif [ "$TESTS_TOTAL" -eq 0 ]; then echo "⚠️"; else echo "❌"; fi)

# 📝 Git Status
GIT_MODIF=$(git status --short 2>/dev/null | wc -l)
GIT_AHEAD=$(git rev-list --count origin/master..master 2>/dev/null || echo "?")
GIT_BEHIND=$(git rev-list --count master..origin/master 2>/dev/null || echo "?")
GIT_STATUS=$(if [ "$GIT_MODIF" -eq 0 ]; then echo "✅"; else echo "⚠️"; fi)

# 🎯 Prochaine Tâche
NEXT_TASK=$(grep -E "^\*\*T[0-9]+\.|^\*\*Prochaine" HEARTBEAT_STATUS.md 2>/dev/null | tail -1 | sed 's/\*\*//g' || echo "T4.1 - VueJS Page accueil")

# 🔴 Problèmes critiques
PROBLEMES=""
[ "$TESTS_PASS" != "$TESTS_TOTAL" ] && PROBLEMES="${PROBLEMES}❌ Tests: $TESTS_PASS/$TESTS_TOTAL\n"
[ "$GIT_MODIF" -gt 0 ] && PROBLEMES="${PROBLEMES}⚠️  Fichiers modifiés: $GIT_MODIF\n"
[ -z "$PROBLEMES" ] && PROBLEMES="✅ Aucun problème critique"

# 📋 Mise à jour HEARTBEAT_STATUS.md
cat > HEARTBEAT_STATUS.md << EOF
# ❤️ Heartbeat Status

**Dernier Check:** $(date '+%Y-%m-%d %H:%M')

## 📊 Tests | ${TEST_STATUS} ${TESTS_PASS}/${TESTS_TOTAL}
- T2.2 Migration ✓ | T2.3 CRUD ✓ | T3.1 Alertes ✓ | T3.2 Dashboard ✓

## 📝 Git | ${GIT_STATUS}
- Divergence: master +${GIT_AHEAD}/-${GIT_BEHIND} vs origin
- Modifs: ${GIT_MODIF}

## 🔴 Problèmes Prioritaires
$(echo -e "$PROBLEMES")

## 🎯 Prochaine Action
**${NEXT_TASK}**

---
*Auto-check toutes les heures | ID: heartbeat-bougies-v2*
EOF

echo "✅ Heartbeat mis à jour: $(date '+%H:%M')"
