<?php
// Script d'archivage complet des tests legacy
$testsToArchive = [
    // Mouvements
    'tests/Feature/Mouvements/CreateMouvementTest.php',
    // Ventes
    'tests/Feature/Ventes/SaleCreationTest.php',
    'tests/Feature/Ventes/SaleHistoryTest.php',
    // Stats
    'tests/Feature/Stats/GlobalStatsTest.php',
    'tests/Feature/Stats/SalesStatsTest.php',
    // Reports  
    'tests/Feature/Reports/InventoryReportTest.php',
    'tests/Feature/Reports/SalesReportControllerTest.php',
    // Debug
    'tests/Feature/Debug/DebugCatalogueTest.php',
    'tests/Feature/Debug/DebugOrderTest.php',
    // Security
    'tests/Feature/Security/SecureCheckoutTest.php',
    'tests/Feature/Security/StockAlertSecurityTest.php',
    'tests/Feature/Security/OrderSecurityTest.php',
    // Configuration
    'tests/Feature/ConfigurationTest.php',
];

foreach ($testsToArchive as $test) {
    if (file_exists($test)) {
        $parts = explode('/', $test);
        $filename = array_pop($parts);
        $category = array_pop($parts);
        
        $destDir = "tests/Feature/$category/.archive/";
        $dest = $destDir . $filename;
        
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
            echo "Créé: $destDir\n";
        }
        
        if (copy($test, $dest)) {
            unlink($test);
            echo "Archivé: $test -> $dest\n";
        }
    }
}

echo "Archivage terminé!";
