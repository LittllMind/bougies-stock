<?php
// Archivage massif des fichiers legacy
function archiveFile($src, $category) {
    $filename = basename($src);
    $destDir = "tests/Feature/$category/.archive/";
    $dest = $destDir . $filename;
    
    if (!file_exists($src)) {
        return false;
    }
    
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    
    if (copy($src, $dest)) {
        unlink($src);
        echo "Archivé: $src -> $dest\n";
        return true;
    }
    return false;
}

// Archiver via glob pour les répertoires
$globDirs = [
    'Security', 'User', 'Reports', 'Admin'
];

foreach ($globDirs as $dir) {
    $pattern = "tests/Feature/$dir/*.php";
    $files = glob($pattern);
    foreach ($files as $file) {
        archiveFile($file, $dir);
    }
}

// Archiver Stats individuellement
$statsFiles = ['ChartsTest.php', 'SalesByPeriodTest.php', 'StocksStatsTest.php'];
foreach ($statsFiles as $file) {
    archiveFile("tests/Feature/Stats/$file", 'Stats');
}

// Archiver Performance
$perfFiles = ['StatsPerformanceTest.php', 'KiosquePerformanceTest.php'];
foreach ($perfFiles as $file) {
    archiveFile("tests/Feature/Performance/$file", 'Performance');
}

echo "Archivage massif terminé!";
