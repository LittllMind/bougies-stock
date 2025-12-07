<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mode Kiosque - Stock Vinyles</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="kiosque-container" x-data="kioskApp()">
        <div class="kiosque-header">
            <h1 style="margin: 0 0 15px 0; font-size: 28px;">🎵 Catalogue Vinyles</h1>
            <input 
                type="text" 
                x-model="search" 
                placeholder="Rechercher par nom ou modèle..."
                class="kiosque-search">
            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button @click="showAll = !showAll" class="btn btn-secondary">
                    <span x-text="showAll ? 'Masquer rupture de stock' : 'Afficher tous'"></span>
                </button>
            </div>
        </div>

        <div class="kiosque-grid">
            <template x-for="vinyle in filteredVinyles" :key="vinyle.id">
                <div 
                    class="kiosque-card" 
                    :class="{ 'selected': isInCart(vinyle.id) }"
                    @click="toggleVinyle(vinyle)">
                    <img 
                        :src="vinyle.photo || '/images/no-image.png'" 
                        :alt="vinyle.nom"
                        class="kiosque-image">
                    <div class="kiosque-content">
                        <h3 class="kiosque-title" x-text="vinyle.nom"></h3>
                        <p class="kiosque-subtitle" x-text="vinyle.modele"></p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="kiosque-price" x-text="formatPrice(vinyle.prix)"></span>
                            <span class="kiosque-stock" x-text="`Stock: ${vinyle.quantite}`"></span>
                        </div>
                        <template x-if="isInCart(vinyle.id)">
                            <div style="margin-top: 10px; padding: 8px; background: #4F46E5; color: white; border-radius: 4px; text-align: center; font-weight: bold;">
                                <span x-text="`${getCartItem(vinyle.id).quantite} dans le panier`"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Panier fixé en bas -->
        <div class="kiosque-cart" x-show="cart.length > 0" x-transition>
            <div class="cart-content">
                <div class="cart-items">
                    <strong>Panier:</strong>
                    <span x-text="cart.length"></span> article(s)
                </div>
                <div class="cart-total">
                    Total: <span x-text="formatPrice(cartTotal)"></span>
                </div>
                <div class="cart-buttons">
                    <button @click="clearCart()" class="btn-large btn-clear">
                        🗑️ Vider
                    </button>
                    <button @click="openCheckout()" class="btn-large btn-sell">
                        💳 Vendre
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de sélection de quantité -->
        <div class="modal" :class="{ 'show': showQuantityModal }">
            <div class="modal-content">
                <h2 style="margin-top: 0;">Ajouter au panier</h2>
                <template x-if="selectedVinyle">
                    <div>
                        <h3 x-text="selectedVinyle.nom"></h3>
                        <p x-text="selectedVinyle.modele"></p>
                        <p><strong x-text="formatPrice(selectedVinyle.prix)"></strong></p>
                        
                        <div class="quantity-selector">
                            <button @click="decrementQuantity()" class="quantity-btn">-</button>
                            <span class="quantity-value" x-text="selectedQuantity"></span>
                            <button @click="incrementQuantity()" class="quantity-btn">+</button>
                        </div>

                        <div class="form-group">
                            <label>Fond (optionnel)</label>
                            <input 
                                type="text" 
                                x-model="selectedFond"
                                placeholder="Ex: Transparent, Noir..."
                                class="form-input">
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button @click="closeQuantityModal()" class="btn btn-secondary" style="flex: 1;">
                                Annuler
                            </button>
                            <button @click="addToCart()" class="btn btn-primary" style="flex: 1;">
                                Ajouter
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Modal de paiement -->
        <div class="modal" :class="{ 'show': showCheckoutModal }">
            <div class="modal-content">
                <h2 style="margin-top: 0;">Finaliser la vente</h2>
                
                <div class="checkout-summary">
                    <h3>Récapitulatif</h3>
                    <template x-for="item in cart" :key="item.id">
                        <div class="checkout-item">
                            <span x-text="`${item.nom} (${item.modele})`"></span>
                            <span x-text="`${item.quantite} x ${formatPrice(item.prix)}`"></span>
                            <strong x-text="formatPrice(item.quantite * item.prix)"></strong>
                        </div>
                    </template>
                    <div class="checkout-total">
                        <strong>Total:</strong>
                        <strong x-text="formatPrice(cartTotal)"></strong>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mode de paiement *</label>
                    <select x-model="modePaiement" class="form-input">
                        <option value="">Sélectionner...</option>
                        <option value="especes">Espèces</option>
                        <option value="carte">Carte bancaire</option>
                        <option value="cheque">Chèque</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button @click="closeCheckout()" class="btn btn-secondary" style="flex: 1;">
                        Annuler
                    </button>
                    <button 
                        @click="confirmSale()" 
                        class="btn btn-primary" 
                        style="flex: 1;"
                        :disabled="!modePaiement">
                        Confirmer la vente
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation -->
        <div class="modal" :class="{ 'show': showSuccessModal }">
            <div class="modal-content" style="text-align: center;">
                <div style="font-size: 64px; margin-bottom: 20px;">✅</div>
                <h2>Vente réussie !</h2>
                <p style="font-size: 24px; color: #10B981; font-weight: bold;" x-text="formatPrice(lastSaleTotal)"></p>
                <button @click="closeSuccess()" class="btn btn-primary btn-large" style="margin-top: 20px;">
                    Nouvelle vente
                </button>
            </div>
        </div>
    </div>

    <script>
        const vinylesData = @json($vinylesData);

        function kioskApp() {
            return {
                vinyles: vinylesData,
                search: '',
                showAll: false,
                cart: [],
                showQuantityModal: false,
                showCheckoutModal: false,
                showSuccessModal: false,
                selectedVinyle: null,
                selectedQuantity: 1,
                selectedFond: '',
                modePaiement: '',
                lastSaleTotal: 0,

                get filteredVinyles() {
                    return this.vinyles.filter(v => {
                        const matchesSearch = v.nom.toLowerCase().includes(this.search.toLowerCase()) ||
                                            v.modele.toLowerCase().includes(this.search.toLowerCase());
                        const inStock = this.showAll || v.quantite > 0;
                        return matchesSearch && inStock;
                    });
                },

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.prix * item.quantite), 0);
                },

                toggleVinyle(vinyle) {
                    if (vinyle.quantite <= 0) {
                        alert('Ce vinyle est en rupture de stock');
                        return;
                    }

                    this.selectedVinyle = vinyle;
                    this.selectedQuantity = 1;
                    this.selectedFond = '';
                    this.showQuantityModal = true;
                },

                incrementQuantity() {
                    if (this.selectedQuantity < this.selectedVinyle.quantite) {
                        this.selectedQuantity++;
                    }
                }
            };
        }
    </script>
</body>
</html>
