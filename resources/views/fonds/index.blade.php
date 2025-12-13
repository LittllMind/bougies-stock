<x-app-layout>
    <x-slot name="header">
        <h2>Gestion des fonds</h2>
    </x-slot>

    <div class="page-content">
        @if (session('success'))
            <div
                style="margin-bottom: 1rem; padding: 0.75rem 1rem; background-color: #DEF7EC; color: #03543F; border-radius: 0.5rem;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                style="margin-bottom: 1rem; padding: 0.75rem 1rem; background-color: #FDE8E8; color: #9B1C1C; border-radius: 0.5rem;">
                <ul style="margin: 0; padding-left: 1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem;">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">Stocks de fonds spéciaux</h3>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #E5E7EB;">Type</th>
                        <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #E5E7EB;">Quantité</th>
                        <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #E5E7EB;">Prix d'achat
                        </th>
                        <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #E5E7EB;">Valeur du stock
                        </th>
                        <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #E5E7EB;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fonds as $fond)
                        @php
                            $valeurStock = $fond->quantite * $fond->prix_achat;
                        @endphp
                        <tr>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #F3F4F6; text-transform: capitalize;">
                                {{ $fond->type }}
                            </td>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #F3F4F6;">
                                <form method="POST" action="{{ route('fonds.update', $fond) }}"
                                    style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantite" min="0"
                                        value="{{ old('quantite', $fond->quantite) }}"
                                        style="width: 80px; padding: 0.25rem 0.5rem; border: 1px solid #D1D5DB; border-radius: 0.375rem;">
                                    <button type="submit"
                                        style="padding: 0.25rem 0.75rem; background-color: #4F46E5; color: white; border-radius: 0.375rem; border: none; cursor: pointer;">
                                        Enregistrer
                                    </button>
                                </form>
                            </td>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #F3F4F6;">
                                {{ number_format($fond->prix_achat, 2, ',', ' ') }} €
                            </td>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #F3F4F6;">
                                {{ number_format($valeurStock, 2, ',', ' ') }} €
                            </td>
                            <td style="padding: 0.5rem; border-bottom: 1px solid #F3F4F6; color: #6B7280;">
                                Modifier la quantité puis cliquer sur "Enregistrer"
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 0.75rem; text-align: center; color: #6B7280;">
                                Aucun fond configuré pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
