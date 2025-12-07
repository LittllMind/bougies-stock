<x-app-layout>
    <x-slot name="header">
        <h2>{{ $vinyle->exists ? 'Modifier' : 'Ajouter' }} un vinyle</h2>
    </x-slot>

    <div class="page-content">
        <div class="form-container">
            <form
                method="POST"
                action="{{ $vinyle->exists ? route('vinyles.update', $vinyle) : route('vinyles.store') }}"
                enctype="multipart/form-data"
                x-data="photoPreview()">

                @csrf
                @if($vinyle->exists)
                @method('PUT')
                @endif

                <div class="form-group">
                    <label for="nom">Nom du vinyle *</label>
                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        value="{{ old('nom', $vinyle->nom) }}"
                        required
                        class="form-input @error('nom') error @enderror">
                    @error('nom')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="modele">Modèle/Artiste *</label>
                    <input
                        type="text"
                        id="modele"
                        name="modele"
                        value="{{ old('modele', $vinyle->modele) }}"
                        required
                        class="form-input @error('modele') error @enderror">
                    @error('modele')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prix">Prix (€) *</label>
                        <input
                            type="number"
                            id="prix"
                            name="prix"
                            value="{{ old('prix', $vinyle->prix) }}"
                            step="0.01"
                            min="0"
                            required
                            class="form-input @error('prix') error @enderror">
                        @error('prix')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="quantite">Quantité *</label>
                        <input
                            type="number"
                            id="quantite"
                            name="quantite"
                            value="{{ old('quantite', $vinyle->quantite) }}"
                            min="0"
                            required
                            class="form-input @error('quantite') error @enderror">
                        @error('quantite')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if($vinyle->exists && $vinyle->hasMedia('photos'))
                <div class="form-group">
                    <label>Photos actuelles</label>
                    <div class="photo-grid">
                        @foreach($vinyle->getMedia('photos') as $media)
                        <div class="photo-item">
                            <img src="{{ $media->getUrl('thumb') }}" alt="Photo">
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    name="delete_photos[]"
                                    value="{{ $media->id }}">
                                Supprimer
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label for="photos">{{ $vinyle->exists ? 'Ajouter des photos' : 'Photos' }} (max 3)</label>
                    <input
                        type="file"
                        id="photos"
                        name="photos[]"
                        multiple
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        @change="previewPhotos($event)"
                        class="form-input @error('photos.*') error @enderror">
                    <small>Formats acceptés : JPEG, PNG, WebP (max 5 Mo par image)</small>
                    @error('photos.*')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div x-show="previews.length > 0" class="photo-grid">
                    <template x-for="(preview, index) in previews" :key="index">
                        <div class="photo-item">
                            <img :src="preview" alt="Aperçu">
                        </div>
                    </template>
                </div>

                <div class="form-actions">
                    <a href="{{ route('vinyles.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        {{ $vinyle->exists ? 'Modifier' : 'Ajouter' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function photoPreview() {
            return {
                previews: [],

                previewPhotos(event) {
                    this.previews = [];
                    const files = event.target.files;

                    for (let i = 0; i < Math.min(files.length, 3); i++) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previews.push(e.target.result);
                        };
                        reader.readAsDataURL(files[i]);
                    }
                }
            }
        }
    </script>
</x-app-layout>