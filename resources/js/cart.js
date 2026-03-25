// Cart App Vue.js
const { createApp } = Vue;

createApp({
    data() {
        return {
            items: [],
            total: 0,
            itemsCount: 0,
            loading: false,
            error: null
        }
    },
    
    mounted() {
        this.loadCart();
    },
    
    methods: {
        async loadCart() {
            this.loading = true;
            try {
                const response = await fetch('/api/cart', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                
                if (data.items) {
                    this.items = data.items;
                    this.total = data.total || 0;
                    this.itemsCount = data.count || data.items.length;
                } else {
                    this.items = [];
                    this.total = 0;
                    this.itemsCount = 0;
                }
            } catch (error) {
                console.error('Erreur chargement panier:', error);
                this.error = 'Impossible de charger le panier';
            } finally {
                this.loading = false;
            }
        },
        
        async updateQuantity(item) {
            try {
                const response = await fetch(`/api/cart/${item.reference}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ quantite: item.quantite })
                });
                
                if (response.ok) {
                    this.loadCart(); // Recharger pour avoir les totaux à jour
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la mise à jour');
                }
            } catch (error) {
                console.error('Erreur mise à jour:', error);
                alert('Erreur lors de la mise à jour');
            }
        },
        
        async removeItem(item) {
            if (!confirm('Voulez-vous supprimer cet article ?')) return;
            
            try {
                const response = await fetch(`/api/cart/${item.reference}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });
                
                if (response.ok) {
                    this.loadCart();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la suppression');
                }
            } catch (error) {
                console.error('Erreur suppression:', error);
                alert('Erreur lors de la suppression');
            }
        },
        
        async clearCart() {
            if (!confirm('Voulez-vous vider tous les articles du panier ?')) return;
            
            try {
                const response = await fetch('/api/cart', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });
                
                if (response.ok) {
                    this.items = [];
                    this.total = 0;
                    this.itemsCount = 0;
                }
            } catch (error) {
                console.error('Erreur vidage:', error);
                alert('Erreur lors du vidage du panier');
            }
        },
        
        formatPrice(price) {
            return parseFloat(price).toFixed(2).replace('.', ',');
        }
    }
}).mount('#cart-app');
