import { createApp, ref, computed, onMounted } from 'vue';

// Composant Carte Bougie
const BougieCard = {
    props: ['bougie'],
    template: `
        <div class="card h-100 bougie-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-secondary">{{ bougie.parfum }}</span>
                    <span class="text-muted small">{{ bougie.format }}</span>
                </div>
                <h5 class="card-title">{{ bougie.nom }}</h5>
                <p class="card-text text-muted small">
                    {{ bougie.collection || 'Sans collection' }}
                </p>
                <p class="card-text">
                    <span class="text-primary fw-bold">{{ bougie.prix }}€</span>
                    <span class="text-muted small ms-2" v-if="bougie.temps_brulure">
                        ({{ bougie.temps_brulure }}h de brûlure)
                    </span>
                </p>
            </div>
            <div class="card-footer bg-white border-0">
                <button 
                    class="btn btn-primary w-100" 
                    @click="$emit('add-to-cart', bougie)"
                    :disabled="bougie.quantite <= 0"
                >
                    <span v-if="bougie.quantite > 0">
                        Ajouter au panier
                    </span>
                    <span v-else>
                        Rupture de stock
                    </span>
                </button>
            </div>
        </div>
    `
};

const app = createApp({
    components: {
        'bougie-card': BougieCard
    },
    setup() {
        // Données injectées par le serveur
        const bougies = ref(window.initialBougies || []);
        const parfums = ref(window.initialParfums || []);
        const collections = ref(window.initialCollections || []);
        
        // Filtres
        const filtreParfum = ref('');
        const filtreCollection = ref('');
        const prixMax = ref(0);
        
        // Tri
        const tri = ref('created_at-desc');
        
        // Calculs
        const prixMin = computed(() => {
            if (bougies.value.length === 0) return 0;
            return Math.min(...bougies.value.map(b => parseFloat(b.prix)));
        });
        
        const prixMaxGlobal = computed(() => {
            if (bougies.value.length === 0) return 100;
            return Math.max(...bougies.value.map(b => parseFloat(b.prix)));
        });
        
        // Initialiser prixMax à la valeur max
        onMounted(() => {
            if (prixMax.value === 0 && prixMaxGlobal.value > 0) {
                prixMax.value = prixMaxGlobal.value;
            }
        });
        
        // Bougies filtrées et triées
        const bougiesFiltrees = computed(() => {
            let result = [...bougies.value];
            
            // Filtres
            if (filtreParfum.value) {
                result = result.filter(b => b.parfum === filtreParfum.value);
            }
            
            if (filtreCollection.value) {
                result = result.filter(b => b.collection === filtreCollection.value);
            }
            
            if (prixMax.value) {
                result = result.filter(b => parseFloat(b.prix) <= prixMax.value);
            }
            
            // Tri
            const [champ, direction] = tri.value.split('-');
            result.sort((a, b) => {
                let valA = a[champ];
                let valB = b[champ];
                
                // Conversion numérique pour le prix
                if (champ === 'prix') {
                    valA = parseFloat(valA);
                    valB = parseFloat(valB);
                }
                
                if (valA < valB) return direction === 'asc' ? -1 : 1;
                if (valA > valB) return direction === 'asc' ? 1 : -1;
                return 0;
            });
            
            return result;
        });
        
        // Méthodes
        const resetFiltres = () => {
            filtreParfum.value = '';
            filtreCollection.value = '';
            prixMax.value = prixMaxGlobal.value;
        };
        
        const ajouterAuPanier = (bougie) => {
            // TODO: Intégrer avec le système de panier existant
            console.log('Ajout au panier:', bougie);
            alert(`"${bougie.nom}" ajoutée au panier !`);
        };
        
        return {
            bougies,
            parfums,
            collections,
            filtreParfum,
            filtreCollection,
            prixMax,
            prixMin,
            prixMaxGlobal,
            tri,
            bougiesFiltrees,
            resetFiltres,
            ajouterAuPanier
        };
    }
});

app.mount('#catalogue-app');
