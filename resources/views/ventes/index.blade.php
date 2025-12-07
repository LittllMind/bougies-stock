<x-app-layout>
    <x-slot name="header">
        <div class="header-actions">
            <h2>Historique des Ventes</h2>
            <a href="{{ route('ventes.create') }}" class="btn btn-primary">+ Nouvelle vente</a>
        </div>
    </x-slot>

    <div class="page-content">
        <div class="table-responsive">
            <table class="vinyle-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Articles</th>
                        <th>Total</th>
                        <th>Paiement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventes as $vente)
                    <tr>
                        <td>{{ $vente->date->format('d/m/Y') }}</td>
                        <td>{{ $vente->lignes->count() }} article(s)</td>
                        <td><strong>{{ number_format($vente->total, 2) }} €</strong></td>
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst($vente->mode_paiement) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('ventes.show', $vente) }}" class="btn btn-sm btn-secondary">
                                Détails
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucune vente enregistrée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $ventes->links() }}
        </div>
    </div>
</x-app-layout>