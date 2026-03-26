# Tests Archivés - Legacy Vinyles/Fonds

Ce dossier contient les tests liés aux anciennes entités Vinyle et Fond qui ont été remplacées par le modèle Bougie.

## Structure

- `Orders/` - Tests commandes legacy (vinyles/fonds)
- `Reports/` - Tests rapports (MonthlyReport, StockReport, etc.)
- `Performance/` - Tests performance (Kiosque, Stats)
- `Security/` - Tests sécurité spécifiques fonds
- `ModeMarche/` - Tests mode marché (vinyles)
- `Mouvements/` - Tests mouvements stock (vinyles/fonds)
- `Stats/` - Tests statistiques (vinyles)
- `Api/` - Tests API ModeMarche (vinyles)

## Pour réactiver un test

1. Mettre à jour pour utiliser le modèle Bougie
2. Déplacer vers `../` (racine tests/Feature/)
3. Mettre à jour les factories utilisées

## Dernière mise à jour

2026-03-26 - Archivage massif des tests legacy après migration vers Bougie
