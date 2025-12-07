<x-app-layout>
    <x-slot name="header">
        <h2>Nouvelle Vente</h2>
    </x-slot>

    {{-- Mise en page + responsive --}}
    <style>
        /* ===== Structure générale ===== */

        .page-content {
            padding: 1.5rem 1rem;
        }

        .form-container {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
            box-sizing: border-box;
            overflow-x: hidden;
        }

        h3 {
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
        }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .form-row-top {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-row-top .form-group {
            flex: 1 1 200px;
            min-width: 0;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-input {
            width: 100%;
            max-width: 100%;
            min-height: 40px;
            padding: 0.45rem 0.6rem;
            font-size: 0.9rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            background-color: #fff;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.24);
        }

        /* Variante compacte pour les lignes d’articles */
        .form-input-compact {
            min-height: 32px;
            padding: 0.25rem 0.45rem;
            font-size: 0.85rem;
        }

        /* ===== Zone "Articles à vendre" ===== */

        .card-articles {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem 0.9rem 0.9rem;
            background-color: #f9fafb;
        }

        .vente-items-header {
            display: grid;
            grid-template-columns: 3fr 0.8fr 1.5fr 1fr auto;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7280;
            padding: 0 0.15rem 0.3rem;
        }

        .vente-item {
            border-top: 1px solid #e5e7eb;
            padding: 0.35rem 0.15rem;
        }

        .vente-item-row {
            display: grid;
            grid-template-columns: 3fr 0.8fr 1.5fr 1fr auto;
            gap: 0.5rem;
            align-items: center;
        }

        /* Important pour que les selects ne débordent jamais */
        .vente-item-row>.form-group {
            min-width: 0;
        }

        .col-vinyle {}

        .col-quantite {}

        .col-fond {}

        .col-prix {}

        .col-delete {
            text-align: right;
        }

        .field-label-mobile {
            display: none;
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 0.15rem;
        }

        /* ===== Total + actions ===== */

        .vente-total {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.25rem;
            padding: 0.75rem 0.9rem;
            border-radius: 0.75rem;
            background: #eef2ff;
            font-size: 1rem;
        }

        .vente-total span {
            font-weight: 600;
            color: #4f46e5;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        /* ===== Boutons ===== */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            padding: 0.4rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            border-width: 1px;
            border-style: solid;
        }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .btn-secondary {
            background-color: #111827;
            border-color: #111827;
            color: #f9fafb;
        }

        .btn-secondary:hover {
            background-color: #020617;
            border-color: #020617;
        }

        .btn-danger {
            background-color: #ef4444;
            border-color: #ef4444;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            border-color: #dc2626;
        }

        .btn-sm {
            padding: 0.15rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 0.375rem;
        }

        /* ===== Responsive mobile ===== */

        @media (max-width: 768px) {
            .form-container {
                padding: 1rem 0.9rem;
                box-shadow: none;
                border-radius: 0;
            }

            .form-row-top {
                flex-direction: column;
            }

            .card-articles {
                padding: 0.65rem 0.6rem 0.8rem;
                border-radius: 0.75rem;
                background-color: #ffffff;
            }

            /* On cache l’en-tête style tableau en mobile */
            .vente-items-header {
                display: none;
            }

            /* Chaque article devient une petite carte sur plusieurs lignes */
            .vente-item-row {
                grid-template-columns: repeat(2, 1fr);
                grid-template-areas:
                    "vinyle vinyle"
                    "quantite fond"
                    "prix delete";
                align-items: flex-start;
            }

            .col-vinyle {
                grid-area: vinyle;
            }

            .col-quantite {
                grid-area: quantite;
            }

            .col-fond {
                grid-area: fond;
            }

            .col-prix {
                grid-area: prix;
            }

            .col-delete {
                grid-area: delete;
                text-align: right;
            }

            .field-label-mobile {
                display: block;
            }

            .form-input-compact {
                min-height: 38px;
                font-size: 0.9rem;
            }
        }
    </style>

    <div class="page-content" x-data="venteForm()">
        <form @submit.prevent="submitForm" class="form-container">
            @csrf

            {{-- Ligne date + mode de paiement --}}
            <div class="form-row-top">
                <div class="form-group">
                    <label for="date">Date *</label>
                    <input type="date" id="date" x-model="formData.date" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="mode_paiement">Mode de paiement *</label>
                    <select id="mode_paiement" x-model="formData.mode_paiement" required class="form-input">
                        <option value="">Sélectionner...</option>
                        <option value="especes">Espèces</option>
                        <option value="carte">Carte bancaire</option>
                        <option value="cheque">Chèque</option>
                    </select>
                </div>
            </div>

            {{-- Articles --}}
            <h3>Articles à vendre</h3>

            <div class="card-articles">
                {{-- En-tête desktop --}}
                <div class="vente-items-header">
                    <div>Vinyle</div>
                    <div>Qté</div>
                    <div>Fond</div>
                    <div>PU</div>
                    <div></div>
                </div>

                {{-- Lignes d’articles --}}
                <template x-for="(item, index) in items" :key="index">
                    <div class="vente-item">
                        <div class="vente-item-row">
                            {{-- Vinyle --}}
                            <div class="form-group col-vinyle">
                                <label class="field-label-mobile">Vinyle *</label>
                                <select x-model="item.id" @change="updateItemPrice(index, $event)" required
                                    class="form-input form-input-compact">
                                    <option value="">Sélectionner un vinyle...</option>
                                    @foreach ($vinyles as $vinyle)
                                        <option value="{{ $vinyle->id }}" data-prix="{{ $vinyle->prix }}"
                                            data-stock="{{ $vinyle->quantite }}">
                                            {{ $vinyle->nom }} - {{ $vinyle->modele }}
                                            (Stock: {{ $vinyle->quantite }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quantité --}}

                            <div class="form-group col-quantite">
                                <label class="field-label-mobile">Quantité *</label>
                                <input type="number" x-model="item.quantite" min="1" :max="item.stock"
                                    @input="calculateTotal" required class="form-input form-input-compact text-center">
                            </div>

                            {{-- Fond --}}
                            <div class="form-group col-fond">
                                <label class="field-label-mobile">Fond</label>
                                <select x-model="item.fond" @change="updateFond(index)"
                                    class="form-input form-input-compact">
                                    <option value="standard">Standard (par défaut)</option>
                                    <option value="miroir">Fond miroir (+8 €)</option>
                                    <option value="dore">Fond doré (+13 €)</option>
                                </select>
                            </div>

                            {{-- Prix unitaire (base + surcoût fond) --}}
                            <div class="form-group col-prix">
                                <label class="field-label-mobile">Prix unitaire</label>
                                <input type="text" :value="formatPrice(item.prix)" readonly
                                    class="form-input form-input-compact text-right">
                            </div>

                            {{-- Bouton supprimer --}}
                            <div class="form-group col-delete">
                                <button type="button" @click="removeItem(index)" class="btn btn-danger btn-sm"
                                    :disabled="items.length === 1">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem" class="btn btn-secondary" style="margin-top: 0.75rem;">
                    + Ajouter un article
                </button>
            </div>

            {{-- Total --}}
            <div class="vente-total">
                Total : <span x-text="formatPrice(total)" class="ml-1"></span> €
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('ventes.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer la vente</button>
            </div>
        </form>
    </div>

    <script>
        function venteForm() {
            return {
                formData: {
                    date: new Date().toISOString().split('T')[0],
                    mode_paiement: 'carte',
                },

                // Surcoûts selon le fond
                fondSupplements: {
                    standard: 0,
                    miroir: 8,
                    dore: 13,
                },

                items: [{
                    id: '',
                    quantite: 1,
                    fond: 'standard',
                    basePrix: 0, // prix du vinyle seul
                    prix: 0, // prix unitaire final (base + fond)
                    stock: 0
                }],

                total: 0,

                addItem() {
                    this.items.push({
                        id: '',
                        quantite: 1,
                        fond: 'standard',
                        basePrix: 0,
                        prix: 0,
                        stock: 0
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    }
                },

                // Quand on change le vinyle
                updateItemPrice(index, event) {
                    const select = event.target;
                    const option = select.options[select.selectedIndex];

                    if (option && option.value) {
                        const base = parseFloat(option.dataset.prix || 0);
                        this.items[index].basePrix = base;
                        this.items[index].stock = parseInt(option.dataset.stock || 0);

                        this.recalcItem(index);
                        this.calculateTotal();
                    }
                },

                // Quand on change le fond
                updateFond(index) {
                    this.recalcItem(index);
                    this.calculateTotal();
                },

                // Recalcule le prix unitaire pour 1 article (base + surcoût fond)
                recalcItem(index) {
                    const item = this.items[index];
                    const base = parseFloat(item.basePrix || 0);
                    const extra = this.fondSupplements[item.fond] || 0;

                    item.prix = base + extra;
                },

                // Recalcule le total de la vente
                calculateTotal() {
                    this.total = this.items.reduce((sum, item) => {
                        const qte = parseFloat(item.quantite || 0);
                        const prix = parseFloat(item.prix || 0);
                        return sum + (prix * qte);
                    }, 0);
                },

                formatPrice(price) {
                    return parseFloat(price || 0).toFixed(2);
                },

                async submitForm() {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('date', this.formData.date);
                    formData.append('mode_paiement', this.formData.mode_paiement);

                    this.items.forEach((item, index) => {
                        formData.append(`vinyles[${index}][id]`, item.id);
                        formData.append(`vinyles[${index}][quantite]`, item.quantite);
                        formData.append(`vinyles[${index}][fond]`, item.fond);
                    });


                    try {
                        const response = await fetch('{{ route('ventes.store') }}', {
                            method: 'POST',
                            body: formData,
                        });

                        if (response.ok) {
                            window.location.href = '{{ route('ventes.index') }}';
                        } else {
                            alert('Erreur lors de l\'enregistrement');
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Erreur réseau');
                    }
                }
            }
        }
    </script>
</x-app-layout>
