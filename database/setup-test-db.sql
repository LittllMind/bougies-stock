-- Script de création de la base de données de test
-- Exécuter avec: mysql -u root -p < database/setup-test-db.sql

CREATE DATABASE IF NOT EXISTS bougies_stock_test 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Vérification
SELECT 'Base bougies_stock_test créée avec succès' AS status;
