<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - {{ config('app.name') }}</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #F5F5DC; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        
        .confirmation-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        h1 { color: #228B22; margin-bottom: 1rem; }
        
        .reference {
            background: #f8f9fa;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-family: monospace;
            font-size: 1.2rem;
            color: #D4AF37;
            display: inline-block;
            margin: 1rem 0;
        }
        
        .order-details {
            text-align: left;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .order-details h3 {
            margin-bottom: 1rem;
            color: #333;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .total {
            font-size: 1.25rem;
            font-weight: bold;
            color: #D4AF37;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: #D4AF37;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
        }
        
        .btn:hover {
            background: #b8962e;
        }
        
        .loading {
            text-align: center;
            padding: 3rem;
        }
        
        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div id="confirmationApp">
        <div class="container">
            <div v-if="loading" class="confirmation-card loading">
                <h2>Chargement de votre commande...</h2>
            </div>
            
            <div v-else-if="error" class="confirmation-card">
                <div class="error">
                    <h2>⚠️ Commande non trouvée</h2>
                    <p>{{ error }}</p>
                    <a href="/catalogue" class="btn">Retour au catalogue</a>
                </div>
            </div>
            
            <div v-else class="confirmation-card">
                <div class="success-icon">✅</div>
                <h1>Merci pour votre commande !</h1>
                
                <p>Votre commande a été confirmée.</p>
                <div class="reference">{{ order.reference }}</div>
                
                <div class="order-details">
                    <h3>📋 Détails de la commande</h3>
                    <div class="detail-row">
                        <span>Client</span>
                        <span>{{ order.nom_client }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Email</span>
                        <span>{{ order.email }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Adresse</span>
                        <span>{{ order.adresse }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Ville</span>
                        <span>{{ order.ville }} {{ order.code_postal }}</span>
                    </div>
                    <div v-if="order.telephone" class="detail-row">
                        <span>Téléphone</span>
                        <span>{{ order.telephone }}</span>
                    </div>
                    <div class="detail-row" v-if="order.total">
                        <span>Total</span>
                        <span class="total">{{ formatPrice(order.total) }}</span>
                    </div>
                </div>
                
                <p>Un email de confirmation a été envoyé à <strong>{{ order.email }}</strong></p>
                
                <a href="/catalogue" class="btn">Continuer mes achats</a>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    loading: true,
                    error: null,
                    order: {}
                };
            },
            
            mounted() {
                this.loadOrder();
            },
            
            methods: {
                async loadOrder() {
                    const reference = '{{ $reference }}';
                    
                    try {
                        const response = await fetch(`/api/orders/${reference}`);
                        const data = await response.json();
                        
                        if (response.ok) {
                            this.order = data.order;
                        } else {
                            this.error = data.message || 'Commande non trouvée';
                        }
                    } catch (err) {
                        this.error = 'Erreur de chargement de la commande';
                    } finally {
                        this.loading = false;
                    }
                },
                
                formatPrice(price) {
                    if (!price) return '-';
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'currency',
                        currency: 'EUR'
                    }).format(price);
                }
            }
        }).mount('#confirmationApp');
    </script>
</body>
</html>