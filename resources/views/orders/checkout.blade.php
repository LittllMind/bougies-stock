<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande - {{ config('app.name') }}</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #F5F5DC; color: #333; line-height: 1.6; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        h1 { text-align: center; margin-bottom: 2rem; color: #D4AF37; }
        
        .checkout-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #D4AF37;
            outline: none;
        }
        
        .form-group.has-error input {
            border-color: #dc3545;
        }
        
        .error-text {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        .cart-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.25rem;
            font-weight: bold;
            color: #D4AF37;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #D4AF37;
        }
        
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: #228B22;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }
        
        .btn-submit:hover {
            background: #1a6b1a;
            transform: translateY(-2px);
        }
        
        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .empty-cart {
            text-align: center;
            padding: 3rem;
        }
        
        .empty-cart-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: #D4AF37;
            text-decoration: none;
            font-weight: 600;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .checkout-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div id="checkoutApp">
        <div class="container">
            <!-- Loading -->
            <div v-if="loading" class="checkout-card">
                <div style="text-align: center; padding: 2rem;">
                    <h2>Chargement...</h2>
                </div>
            </div>

            <!-- Panier vide -->
            <div v-else-if="cartItems.length === 0 && !orderSuccess" class="checkout-card empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Votre panier est vide</h2>
                <p>Ajoutez des bougies à votre panier pour passer commande.</p>
                <a href="/catalogue" class="back-link"> Retour au catalogue</a>
            </div>

            <!-- Formulaire de commande -->
            <div v-else-if="!orderSuccess" class="checkout-card">
                <h1>✨ Finaliser votre commande</h1>
                
                <!-- Erreurs générales -->
                <div v-if="errors.length > 0" class="alert alert-error">
                    <ul style="margin-left: 20px;">
                        <li v-for="(e, idx) in errors" :key="idx">{{ e }}</li>
                    </ul>
                </div>

                <!-- Résumé panier -->
                <div class="cart-summary">
                    <h3 style="margin-bottom: 1rem;">🛍️ Résumé de votre panier</h3>
                    <div v-for="item in cartItems" :key="item.reference" class="cart-item">
                        <div>
                            <div style="font-weight: 600;">{{ item.nom }}</div>
                            <div style="color: #666;">{{ item.parfum }} - Quantité: {{ item.quantite }}</div>
                        </div>
                        <div style="font-weight: 600;">
                            {{ formatPrice(item.quantite * item.prix_unitaire) }}
                        </div>
                    </div>
                    <div class="cart-total">
                        <span>Total</span>
                        <span>{{ formatPrice(cartTotal) }}</span>
                    </div>
                </div>

                <!-- Formulaire client -->
                <form @submit.prevent="submitOrder">
                    <h3 style="margin-bottom: 1rem;">👤 Informations personnelles</h3>
                    
                    <div class="form-row">
                        <div class="form-group" :class="{ 'has-error': fieldErrors.nom_client }">
                            <label for="nom_client">Nom complet *</label>
                            <input type="text" id="nom_client" v-model="form.nom_client" required>
                            <div v-if="fieldErrors.nom_client" class="error-text">
                                {{ fieldErrors.nom_client[0] }}
                            </div>
                        </div>
                        
                        <div class="form-group" :class="{ 'has-error': fieldErrors.email }">
                            <label for="email">Email *</label>
                            <input type="email" id="email" v-model="form.email" required>
                            <div v-if="fieldErrors.email" class="error-text">
                                {{ fieldErrors.email[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width" :class="{ 'has-error': fieldErrors.adresse }">
                        <label for="adresse">Adresse de livraison *</label>
                        <input type="text" id="adresse" v-model="form.adresse" required>
                        <div v-if="fieldErrors.adresse" class="error-text">
                            {{ fieldErrors.adresse[0] }}
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" :class="{ 'has-error': fieldErrors.code_postal }">
                            <label for="code_postal">Code postal *</label>
                            <input type="text" id="code_postal" v-model="form.code_postal" required>
                            <div v-if="fieldErrors.code_postal" class="error-text">
                                {{ fieldErrors.code_postal[0] }}
                            </div>
                        </div>
                        
                        <div class="form-group" :class="{ 'has-error': fieldErrors.ville }">
                            <label for="ville">Ville *</label>
                            <input type="text" id="ville" v-model="form.ville" required>
                            <div v-if="fieldErrors.ville" class="error-text">
                                {{ fieldErrors.ville[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" v-model="form.telephone">
                    </div>

                    <div class="form-group full-width">
                        <label for="notes_client">Instructions spéciales (optionnel)</label>
                        <textarea id="notes_client" v-model="form.notes_client" rows="3" placeholder="Ex: Livrer après 14h..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit" :disabled="submitting">
                        {{ submitting ? 'Traitement...' : '✅ Confirmer la commande' }}
                    </button>
                </form>
            </div>

            <!-- Confirmation succès -->
            <div v-if="orderSuccess" class="checkout-card" style="text-align: center;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                <h1>Commande confirmée !</h1>
                
                <div class="alert alert-success" style="margin: 1.5rem 0;">
                    <p style="font-size: 1.1rem;">Votre commande <strong>{{ orderReference }}</strong> a été reçue.</p>
                    <p>Un email de confirmation a été envoyé à {{ form.email }}</p>
                </div>
                
                <a href="/catalogue" class="back-link"> Retour au catalogue</a>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    loading: true,
                    cartItems: [],
                    cartTotal: 0,
                    form: {
                        nom_client: '',
                        email: '',
                        adresse: '',
                        code_postal: '',
                        ville: '',
                        telephone: '',
                        notes_client: ''
                    },
                    fieldErrors: {},
                    errors: [],
                    submitting: false,
                    orderSuccess: false,
                    orderReference: ''
                };
            },
            
            async mounted() {
                await this.loadCart();
            },
            
            methods: {
                async loadCart() {
                    try {
                        const response = await fetch('/api/cart');
                        const data = await response.json();
                        
                        this.cartItems = data.items || [];
                        this.cartTotal = data.total || 0;
                    } catch (error) {
                        console.error('Erreur chargement panier:', error);
                        this.errors = ['Impossible de charger le panier'];
                    } finally {
                        this.loading = false;
                    }
                },
                
                formatPrice(price) {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'currency',
                        currency: 'EUR'
                    }).format(price);
                },
                
                async submitOrder() {
                    this.submitting = true;
                    this.errors = [];
                    this.fieldErrors = {};
                    
                    try {
                        const response = await fetch('/api/orders', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            },
                            body: JSON.stringify(this.form)
                        });
                        
                        const data = await response.json();
                        
                        if (response.status === 201) {
                            this.orderSuccess = true;
                            this.orderReference = data.reference;
                            // Vider le localStorage pour synchroniser avec le serveur
                            localStorage.removeItem('cart');
                        } else if (response.status === 422) {
                            this.fieldErrors = data.errors || {};
                            if (data.errors) {
                                this.errors = Object.values(data.errors).flat();
                            }
                        } else if (response.status === 400 || response.status === 422) {
                            this.errors = [data.message];
                        } else {
                            this.errors = [data.message || 'Erreur lors de la commande'];
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        this.errors = ['Erreur de connexion. Veuillez réessayer.'];
                    } finally {
                        this.submitting = false;
                    }
                }
            }
        }).mount('#checkoutApp');
    </script>
</body>
</html>