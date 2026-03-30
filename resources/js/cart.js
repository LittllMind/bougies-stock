// Cart App Vue.js - Version localStorage unifiée
import { createApp } from 'vue';
import { cartService } from './cartService.js';

createApp({
    data() {
        return {
            items: [],
            total: 0,
            itemsCount: 0,
            loading: false,
            syncing: false,
            error: null
        }
    },
    
    mounted() {
        this.loadCart();
        // Écouter les changements de panier depuis d'autres onglets/composants
        window.addEventListener('cart-updated', () => {
            this.loadCart();
        });
        // Écouter storage pour synchronisation entre onglets
        window.addEventListener('storage', (e) => {
            if (e.key === 'bougies_cart') {
                this.loadCart();
            }
        });
    },
    
    methods: {
        loadCart() {
            const cart = cartService.getCart();
            this.items = cart.items || [];
            this.total = cart.total || 0;
            this.itemsCount = cart.count || 0;
        },
        
        async syncAndCheckout() {
            if (this.items.length === 0) {
                alert('Votre panier est vide');
                return;
            }
            
            this.syncing = true;
            
            // Préparer les données à synchroniser - vérifier les références
            const itemsToSync = this.items
                .filter(item => item.reference && item.quantite)
                .map(item => ({
                    reference: String(item.reference).trim(),
                    quantite: parseInt(item.quantite)
                }));
            
            console.log('Synchronisation panier:', itemsToSync);
            
            try {
                // Synchroniser avec le serveur
                const response = await fetch('/api/cart/sync', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                    },
                    body: JSON.stringify({ items: itemsToSync })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Sync réussie:', result);
                    // Rediriger vers le checkout
                    window.location.href = '/orders/create';
                } else {
                    const error = await response.json();
                    console.error('Erreur sync:', error);
                    
                    let message = 'Erreur de synchronisation.';
                    if (error.errors) {
                        const firstError = Object.values(error.errors)[0];
                        message = Array.isArray(firstError) ? firstError[0] : firstError;
                    } else if (error.message) {
                        message = error.message;
                    }
                    alert('Erreur: ' + message);
                }
            } catch (error) {
                console.error('Erreur network:', error);
                alert('Erreur de connexion. Vérifiez votre connexion internet.');
            } finally {
                this.syncing = false;
            }
        },
        
        updateQuantity(item) {
            cartService.updateQuantity(item.reference, item.quantite);
            this.loadCart();
        },
        
        removeItem(item) {
            if (!confirm('Voulez-vous supprimer cet article ?')) return;
            cartService.removeItem(item.reference);
            this.loadCart();
        },
        
        clearCart() {
            if (!confirm('Voulez-vous vider tous les articles du panier ?')) return;
            cartService.clearCart();
            this.loadCart();
        },
        
        formatPrice(price) {
            return parseFloat(price).toFixed(2).replace('.', ',');
        }
    }
}).mount('#cart-app');
