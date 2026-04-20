<?php
// Script pour archiver les migrations vinyles legacy
$migrations = [
    '2026_03_10_104001_add_seuil_alerte_to_vinyles.php',
    '2026_03_10_140000_remove_fond_id_from_vinyles.php',
    '2026_03_25_063000_make_titre_vinyle_nullable.php',
    '2026_03_25_080000_drop_legacy_vinyle_tables.php',
    '2026_03_26_100853_make_vinyle_id_nullable.php',
    '2026_03_26_110853_make_vinyle_id_nullable.php',
];

$sourceDir = __DIR__ . '/../database/migrations/';
$archiveDir = __DIR__ . '/../.archive/migrations/';

if (!is_dir($archiveDir)) {
    mkdir($archiveDir, 0755, true);
}

foreach ($migrations as $migration) {
    $source = $sourceDir . $migration;
    $dest = $archiveDir . $migration;
    
    if (file_exists($source)) {
        rename($source, $dest);
        echo "Archivée: $migration\n";
    } else {
        echo "Déjà archivée: $migration\n";
    }
}

echo "\nTerminé.\n";
