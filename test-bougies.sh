#!/bin/bash
# Script de test pour bougies - vide proprement la BDD avant tests

cd /home/aur-lien/.picoclaw/workspace/bougies-stock

echo "=== Préparation base de test ==="
mysql -u root -e "DROP DATABASE IF EXISTS bougies_stock; CREATE DATABASE bougies_stock;" 2>/dev/null || echo "MySQL drop/create (peut avoir des warnings)"

echo "=== Migration avec seeders ==="
php artisan migrate:fresh --seed --force 2>&1

echo "=== Lancement tests Bougie ==="
php artisan test --filter=Bougie --bail 2>&1 | grep -E "(PASS|FAIL|✓|✗|Tests:)" | tail -50
