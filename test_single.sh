#!/bin/bash
cd /home/aur-lien/.picoclaw/workspace/bougies-stock
php artisan test --filter=CheckoutBougieTest::test_page_paiement_affiche_recapitulatif_commande 2>&1
