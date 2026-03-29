// Service de panier unifié - localStorage
const CART_KEY = 'bougies_cart';

export const cartService = {
    // Récupérer le panier
    getCart() {
        const cart = localStorage.getItem(CART_KEY);
        if (cart) {
            return JSON.parse(cart);
        }
        return { items: [], total: 0, count: 0 };
    },
    
    // Sauvegarder le panier
    saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        // Émettre un événement pour notifier les autres composants
        window.dispatchEvent(new Event('cart-updated'));
    },
    
    // Ajouter une bougie au panier
    addItem(bougie) {
        const cart = this.getCart();
        const existingItem = cart.items.find(item => item.reference === bougie.reference);
        
        if (existingItem) {
            existingItem.quantite++;
            existingItem.sous_total = existingItem.quantite * existingItem.prix_unitaire;
        } else {
            cart.items.push({
                reference: bougie.reference,
                nom: bougie.nom,
                parfum: bougie.parfum,
                prix_unitaire: parseFloat(bougie.prix),
                quantite: 1,
                sous_total: parseFloat(bougie.prix)
            });
        }
        
        this.recalculateTotals(cart);
        this.saveCart(cart);
        return cart;
    },
    
    // Mettre à jour la quantité
    updateQuantity(reference, quantite) {
        const cart = this.getCart();
        const item = cart.items.find(item => item.reference === reference);
        
        if (item) {
            item.quantite = parseInt(quantite);
            item.sous_total = item.quantite * item.prix_unitaire;
            this.recalculateTotals(cart);
            this.saveCart(cart);
        }
        
        return cart;
    },
    
    // Supprimer un article
    removeItem(reference) {
        const cart = this.getCart();
        cart.items = cart.items.filter(item => item.reference !== reference);
        this.recalculateTotals(cart);
        this.saveCart(cart);
        return cart;
    },
    
    // Vider le panier
    clearCart() {
        const emptyCart = { items: [], total: 0, count: 0 };
        this.saveCart(emptyCart);
        return emptyCart;
    },
    
    // Calculer les totaux
    recalculateTotals(cart) {
        cart.count = cart.items.reduce((sum, item) => sum + item.quantite, 0);
        cart.total = cart.items.reduce((sum, item) => sum + item.sous_total, 0);
    },
    
    // Obtenir le nombre d'articles (pour badge)
    getCount() {
        const cart = this.getCart();
        return cart.count || 0;
    }
};
