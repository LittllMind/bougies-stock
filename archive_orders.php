<?php
// Script d'archivage des tests legacy
$sourceDir = __DIR__ . '/tests/Feature/Orders';
$targetDir = __DIR__ . '/tests/Feature/_archive/Orders';

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$files = glob($sourceDir . '/*.php');
foreach ($files as $file) {
    $basename = basename($file);
    $target = $targetDir . '/' . $basename;
    if (rename($file, $target)) {
        echo "Archivé: $basename\n";
    } else {
        echo "Échec: $basename\n";
    }
}

echo "\nArchivage terminé.\n";
