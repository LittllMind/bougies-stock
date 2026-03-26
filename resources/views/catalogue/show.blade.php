<x-app-layout>
    <div id="app-detail" class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="md:flex">
                    <!-- Image placeholder -->
                    <div class="md:w-1/3 bg-gradient-to-br from-amber-100 to-amber-200 h-64 md:h-auto flex items-center justify-center">
                        <svg class="w-24 h-24 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                        </svg>
                    </div>

                    <!-- Info produit -->
                    <div class="md:w-2/3 p-8" id="bougie-data" data-bougie="@json($bougie)">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $bougie->nom }}</h1>
                        
                        <!-- Collection et Parfum -->
                        <div class="flex flex-wrap gap-4 mb-4">
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm">
                                Collection: {{ $bougie->collection }}
                            </span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                                Parfum: {{ $bougie->parfum }}
                            </span>
                        </div>

                        <!-- Prix et stock -->
                        <div class="flex items-baseline gap-4 mb-6">
                            <span class="text-4xl font-bold text-amber-600">{{ number_format($bougie->prix, 2) }} €</span>
                            <span class="px-3 py-1 {{ $bougie->quantite > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full text-sm">
                                {{ $bougie->quantite }} en stock
                            </span>
                        </div>

                        <!-- Caractéristiques -->
                        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                <span><strong>Format:</strong> {{ $bougie->format }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span><strong>Temps brûlure:</strong> {{ $bougie->temps_brulure ?? 'N/A' }} min</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                                <span><strong>Type de cire:</strong> {{ $bougie->type_cire ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($bougie->notes)
                        <div class="mb-6 p-4 bg-amber-50 rounded-lg">
                            <h3 class="font-semibold text-amber-800 mb-2">Notes olfactives</h3>
                            <p class="text-gray-700">{{ $bougie->notes }}</p>
                        </div>
                        @endif

                        <!-- Quantité et ajout panier -->
                        <div class="flex items-center gap-4" v-if="bougie && bougie.quantite > 0">
                            <div class="flex items-center border rounded-lg">
                                <button @click="quantity > 1 ? quantity-- : null" type="button" class="px-4 py-2 text-gray-600 hover:bg-gray-100">-</button>
                                <span class="px-4 py-2 font-semibold">@{{ quantity }}</span>
                                <button @click="quantity++" type="button" class="px-4 py-2 text-gray-600 hover:bg-gray-100">+</button>
                            </div>
                            
                            <button @click="addToCart" type="button" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-3 px-6 rounded-lg font-semibold transition-colors">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    bougie: null,
                    quantity: 1
                }
            },
            mounted() {
                const bougieData = document.getElementById('bougie-data').dataset.bougie;
                this.bougie = JSON.parse(bougieData);
            },
            methods: {
                addToCart() {
                    if (this.bougie && this.quantity > 0) {
                        console.log('Ajout au panier:', this.bougie.reference, 'qty:', this.quantity);
                        // TODO: Implement cart API call
                    }
                }
            }
        }).mount('#app-detail');
    </script>
    @endpush
</x-app-layout>