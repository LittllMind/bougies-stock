#!/bin/bash
# Script de vérification complète du tunnel de vente
# Usage: ./test-tunnel.sh [option]

cd /home/aur-lien/.picoclaw/workspace/bougies-stock

echo "🕯️  Les Bougies de Séraphie - Test Tunnel de Vente"
echo "=================================================="

# Fonction: Afficher titre
title() {
    echo ""
    echo "$1"
    echo "--"
}

# Option: all (défaut), routes, tunnel, checkout, stripe
case "${1:-all}" in
    routes|r)
        title "📋 Routes du tunnel"
        php artisan route:list | grep -E "(cart|orders|payment|kiosque)" | head -20
        ;;
    
    tunnel|t)
        title "🧪 Tests TunnelVenteIntegrationTest"
        php artisan test tests/Feature/Orders/TunnelVenteIntegrationTest.php
        ;;
    
    checkout|c)
        title "🧪 Tests CheckoutBougieTest"
        php artisan test tests/Feature/Orders/CheckoutBougieTest.php
        ;;
    
    stripe|s)
        title "🧪 Tests Stripe"
        php artisan test tests/Feature/Orders/StripeCheckoutTest.php
        php artisan test tests/Feature/Orders/StripeWebhookTest.php
        ;;
    
    email|e)
        title "🧪 Tests Email"
        php artisan test tests/Feature/Orders/OrderConfirmationEmailTest.php
        ;;
    
    db)
        title "📊 État base de données"
        php artisan migrate:status | tail -10
        echo ""
        php artisan tinker --execute="echo 'Bougies: ' . App\Models\Bougie::count() . PHP_EOL; echo 'Users: ' . App\Models\User::count() . PHP_EOL; echo 'Orders: ' . App\Models\Order::count() . PHP_EOL;"
        ;;
    
    all|a|*)
        title "📋 Routes tunnel"
        php artisan route:list | grep -E "(cart|orders|payment|kiosque)" | wc -l | xargs echo "Routes trouvées:"
        
        title "🧪 Tests tunnel"
        php artisan test --filter=TunnelVente
        
        title "🧪 Tests checkout"
        php artisan test --filter=CheckoutBougie
        
        title "🧪 Tests stripe"
        php artisan test --filter=StripeCheckout
        php artisan test --filter=StripeWebhook | tail -10
        ;;
esac

echo ""
echo "✅ Vérification terminée"
