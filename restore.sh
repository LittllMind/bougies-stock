#!/bin/bash
cd /home/aur-lien/.picoclaw/workspace/bougies-stock

git checkout -- app/Http/Controllers/OrderController.php app/Http/Controllers/Api/CartController.php resources/views/orders/create.blade.php resources/views/orders/payment.blade.php resources/views/kiosque.blade.php resources/views/layouts/app.blade.php resources/js/app.js resources/js/cart.js resources/js/catalogue.js resources/views/cart/index.blade.php app/Http/Middleware/TrustHosts.php
