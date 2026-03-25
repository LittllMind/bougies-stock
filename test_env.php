<?php
require 'vendor/autoload.php';

// Load the .env.testing file if it exists
if (file_exists('.env.testing')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env.testing');
    $dotenv->load();
    echo "Loaded .env.testing\n";
} elseif (file_exists('.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "Loaded .env\n";
}

echo "DB_DATABASE: " . getenv('DB_DATABASE') . "\n";
echo "DB_CONNECTION: " . getenv('DB_CONNECTION') . "\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_USERNAME: " . getenv('DB_USERNAME') . "\n";
