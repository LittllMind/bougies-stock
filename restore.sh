#!/bin/bash
cd /home/aur-lien/.picoclaw/workspace/bougies-stock

# Restaurer tous les fichiers modifiés
git restore app/Models/Bougie.php
git restore app/Models/Fond.php
git restore app/Models/LigneVente.php
git restore app/Models/Vente.php
git restore app/Models/Vinyle.php
git restore database/factories/BougieFactory.php
git restore database/factories/FondFactory.php
git restore database/factories/LigneVenteFactory.php
git restore database/factories/MouvementStockFactory.php
git restore database/factories/VenteFactory.php
git restore database/factories/VinyleFactory.php
git restore database/seeders/BougieSeeder.php
git restore database/seeders/DatabaseSeeder.php
git restore database/seeders/FondSeeder.php
git restore database/seeders/VenteSeeder.php
git restore database/seeders/VinyleSeeder.php

echo "Fichiers restaurés"
git status --short