<x-app-layout>
    <x-slot name="header">
        <h2>{{ $vinyle->exists ? 'Modifier' : 'Ajouter' }} un vinyle</h2>
    </x-slot>

    <div class="page-content">
        <div class="form-container">
            <form method="POST"
                action="{{ $vinyle->exists ? route('vinyles.update', $vinyle) : route('vinyles.store') }}"
                enctype="multipart/form-data" x-data="photoPreview()">

                @csrf
                @if ($vinyle->exists)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="nom">Nom du vinyle *</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $vinyle->nom) }}" required
                        class="form-input @error('nom') error @enderror">
                    @error('nom')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="modele">Modèle/Artiste *</label>
                    <input type="text" id="modele" name="modele" value="{{ old('modele', $vinyle->modele) }}"
                        required class="form-input @error('modele') error @enderror">
                    @error('modele')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prix">Prix (€) *</label>
                        <input type="number" id="prix" name="prix" value="{{ old('prix', $vinyle->prix) }}"
                            step="0.01" min="0" required class="form-input @error('prix') error @enderror">
                        @error('prix')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="quantite">Quantité *</label>
                        <input type="number" id="quantite" name="quantite"
                            value="{{ old('quantite', $vinyle->quantite) }}" min="0" required
                            class="form-input @error('quantite') error @enderror">
                        @error('quantite')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>




                <div class="form-group">
                    <label for="photos">Photos du vinyle (max 3)</label>
                    <input type="file" id="photos" name="photos[]" multiple
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="form-input @error('photos') error @enderror @error('photos.*') error @enderror"
                        @change="previewPhotos($event)"
                        >
                    @error('photos')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @error('photos.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror

                    @if ($vinyle->exists && $vinyle->hasMedia('photo'))
    <div class="existing-photos">
        <p>Photos existantes :</p>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin: 10px 0;">
            @foreach($vinyle->getMedia('photo') as $media)
                <div style="position: relative; display: inline-block;">
                    <img src="{{ $media->getUrl('thumb') }}" alt="{{ $vinyle->nom }}" class="thumb-img" style="max-width: 100px; border-radius: 8px;">
                    <label style="display: block; margin-top: 5px; font-size: 12px;">
                        <input type="checkbox" name="delete_photos[]" value="{{ $media->id }}">
                        Supprimer
                    </label>
                </div>
            @endforeach
        </div>
    </div>
@endif
                </div>

                <div x-show="previews.length > 0" class="photo-preview" style="margin-top: 1rem;">
                    <p>Aperçu des nouvelles photos :</p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <template x-for="(preview, index) in previews" :key="index">
                            <img :src="preview" alt="Aperçu" style="max-width: 100px; border-radius: 8px;">
                        </template>
                    </div>
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
                    if (files) {
                        Array.from(files).slice(0, 3).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.previews.push(e.target.result);
                            };
                            reader.readAsDataURL(file);
                        });
                    }
                }
            }
        }
    </script>
</x-app-layout>
