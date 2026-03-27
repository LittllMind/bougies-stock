#!/bin/bash
# Script de nettoyage - à exécuter par l'utilisateur
set -e

echo "🧹 NETTOYAGE WORKSPACE BOUGIES"
echo "================================"

cd /home/aur-lien/.picoclaw/workspace

# 1. Créer structure d'archive
mkdir -p archive/{scripts,logs,temp,docs}

# 2. Déplacer scripts temporaires racine (en gardant SOUL/AGENTS/MEMORY)
for f in SCRIPT_*.sh cleanup_t44.sh check_routes.sh diagnostic_branches.sh; do
    if [ -f "$f" ]; then
        mv "$f" archive/scripts/
        echo "✓ Déplacé: $f"
    fi
done

# 3. Déplacer logs
for f in *.log *.zip *.tmp *.tar 2>/dev/null; do
    if [ -f "$f" ]; then
        mv "$f" archive/logs/
        echo "✓ Déplacé: $f"
    fi
done

# 4. Déplacer docs orphelines
for f in MESSAGE-LEDGER*.md RESEARCH_PICOLCLAW*.md; do
    if [ -f "$f" ]; then
        mv "$f" archive/docs/
        echo "✓ Archivé: $f"
    fi
done

cd bougies-stock

# 5. Supprimer scripts temporaires bougies-stock
rm -f \
    run-vinyles-tests.sh \
    run-t14-tests.sh \
    test-security.sh \
    run-sec-tests.sh \
    run-security-tests.sh \
    *.tmp \
    *.bak \
    commit-t44.sh \
    cleanup_workspace.sh 2>/dev/null || true

echo "✓ Scripts temporaires supprimés"

# 6. Déplacer scripts utiles vers archive
for f in deploy.sh refresh.sh restore.sh 2>/dev/null; do
    if [ -f "$f" ]; then
        cp "$f" ../archive/scripts/
        echo "✓ Backup: $f"
    fi
done

# 7. Nettoyer logs
rm -f heartbeat.log 2>/dev/null || true

echo ""
echo "══════════════════════════════════════"
echo "✅ NETTOYAGE TERMINÉ"
echo "══════════════════════════════════════"
ls -la ../archive/
