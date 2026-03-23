@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Gestion des Bougies</h1>
        <a href="{{ route('admin.bougies.create') }}" class="bg-[#D4AF37] hover:bg-[#B8972E] text-white px-4 py-2 rounded-lg">
            + Nouvelle Bougie
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Référence</th>
                    <th class="px-4 py-3 text-left">Nom</th>
                    <th class="px-4 py-3 text-left">Parfum</th>
                    <th class="px-4 py-3 text-left">Format</th>
                    <th class="px-4 py-3 text-left">Prix</th>
                    <th class="px-4 py-3 text-left">Stock</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bougies as $bougie)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-sm">{{ $bougie->reference }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.bougies.show', $bougie) }}" class="text-[#D4AF37] hover:underline font-medium">
                            {{ $bougie->nom }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ $bougie->parfum }}</td>
                    <td class="px-4 py-3">{{ $bougie->format }}</td>
                    <td class="px-4 py-3">{{ number_format($bougie->prix, 2) }} €</td>
                    <td class="px-4 py-3">
                        @if($bougie->rupture_stock)
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Rupture</span>
                        @elseif($bougie->stock_faible)
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">{{ $bougie->quantite }}</span>
                        @else
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">{{ $bougie->quantite }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.bougies.edit', $bougie) }}" class="text-blue-600 hover:text-blue-800 mr-3">✏️</a>
                        <form action="{{ route('admin.bougies.destroy', $bougie) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette bougie ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">🗑️</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        Aucune bougie enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bougies->links() }}
    </div>
</div>
@endsection
