<?php
// Script pour archiver les tests legacy
$tests = [
    'tests/Feature/BootstrapVueTest.php',
    'tests/Feature/InfrastructureTest.php', 
    'tests/Feature/StockAlertControllerTest.php',
    'tests/Feature/StockAlertDashboardTest.php',
    'tests/Feature/DashboardAdminTest.php',
];

$dirs = [
    'tests/Feature/.archive/legacy',
    'tests/Feature/Api/.archive',
    'tests/Feature/ModeMarche/.archive',
    'tests/Feature/Mouvements/.archive',
    'tests/Feature/Ventes/.archive',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Créé: $dir\n";
    }
}

foreach ($tests as $test) {
    if (file_exists($test)) {
        $dest = str_replace('tests/Feature/', 'tests/Feature/.archive/legacy/', $test);
        if (copy($test, $dest)) {
            unlink($test);
            echo "Archivé: $test -> $dest\n";
        }
    }
}

echo "Archivage terminé!";
