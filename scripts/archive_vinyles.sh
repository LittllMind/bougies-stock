#!/bin/bash
cd /home/aur-lien/.picoclaw/workspace/bougies-stock

# Créer répertoire archive s'il n'existe pas
mkdir -p .archive/migrations

# Archiver les migrations vinyles
cp database/migrations/2026_03_10_104001_add_seuil_alerte_to_vinyles.php .archive/migrations/ 2>/dev/null && echo "1. add_seuil_alerte_to_vinyles -> archivé"
cp database/migrations/2026_03_10_140000_remove_fond_id_from_vinyles.php .archive/migrations/ 2>/dev/null && echo "2. remove_fond_id_from_vinyles -> archivé"
cp database/migrations/2026_03_25_063000_make_titre_vinyle_nullable.php .archive/migrations/ 2>/dev/null && echo "3. make_titre_vinyle_nullable -> archivé"
cp database/migrations/2026_03_26_100853_make_vinyle_id_nullable.php .archive/migrations/ 2>/dev/null && echo "4. make_vinyle_id_nullable (1) -> archivé"
cp database/migrations/2026_03_26_110853_make_vinyle_id_nullable.php .archive/migrations/ 2>/dev/null && echo "5. make_vinyle_id_nullable (2) -> archivé"

# Supprimer les originals
rm -f database/migrations/2026_03_10_104001_add_seuil_alerte_to_vinyles.php
rm -f database/migrations/2026_03_10_140000_remove_fond_id_from_vinyles.php
rm -f database/migrations/2026_03_25_063000_make_titre_vinyle_nullable.php
rm -f database/migrations/2026_03_26_100853_make_vinyle_id_nullable.php
rm -f database/migrations/2026_03_26_110853_make_vinyle_id_nullable.php

echo ""
echo "✓ Archivage terminé"
echo "✓ Migrations vinyles déplacées vers .archive/migrations/"
