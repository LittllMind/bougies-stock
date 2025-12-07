<x-app-layout>
    <x-slot name="header">
        <h2>Détail de la vente du {{ $vente->date->format('d/m/Y') }}</h2>
    </x-slot>

    <div class="page-content">
        <div class="vente-details">
            <div class="vente-info">
                <p><strong>Date :</strong> {{ $vente->date->format('d/m/Y à H:i') }}</p>
                <p><strong>Mode de paiement :</strong> {{ ucfirst($vente->mode_paiement) }}</p>
                <p><strong>Total :</strong> <span class="text-lg">{{ number_format($vente->total, 2) }} €</span></p>
            </div>

            <h3>Articles vendus</h3>
            <div class="table-responsive">
                <table class="vinyle-table">
                    <thead>
                        <tr>
                            <th>Vinyle</th>
                            <th>Modèle</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Fond</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vente->lignes as $ligne)
                        <tr>
                            <td>{{ $ligne->vinyle->nom }}</td>
                            <td>{{ $ligne->vinyle->modele }}</td>
                            <td>{{ number_format($ligne->prix_unitaire, 2) }} €</td>
                            <td>{{ $ligne->quantite }}</td>
                            <td>{{ $ligne->fond ?? '-' }}</td>
                            <td><strong>{{ number_format($ligne->total, 2) }} €</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <a href="{{ route('ventes.index') }}" class="btn btn-secondary">Retour</a>
            </div>
        </div>
    </div>
</x-app-layout>