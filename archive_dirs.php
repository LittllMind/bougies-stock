<?php
// Archiver les dossiers de tests legacy
$mapping = [
    'tests/Feature/Api/ModeMarcheApiTest.php' => 'tests/Feature/Api/.archive/',
    'tests/Feature/ModeMarche/AnnulationVenteTest.php' => 'tests/Feature/ModeMarche/.archive/',
    'tests/Feature/ModeMarche/ApiModeMarcheTest.php' => 'tests/Feature/ModeMarche/.archive/',
    'tests/Feature/ModeMarche/ModeMarcheTest.php' => 'tests/Feature/ModeMarche/.archive/',
    'tests/Feature/ModeMarche/SalesHistoryTest.php' => 'tests/Feature/ModeMarche/.archive/',
    'tests/Feature/ModeMarche/VentesJourTest.php' => 'tests/Feature/ModeMarche/.archive/',
];

foreach ($mapping as $src => $destDir) {
    if (file_exists($src)) {
        $filename = basename($src);
        $dest = $destDir . $filename;
        if (copy($src, $dest)) {
            unlink($src);
            echo "Archivé: $src -> $dest\n";
        }
    }
}

echo "Archivage des dossiers terminé!";
