<?php

namespace App\Http\Controllers;

use App\Models\Bougie;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    /**
     * Diagnostic rapide des bougies
     */
    public function bougies(Request $request)
    {
        $bougies = Bougie::all();
        
        return response()->json([
            'total_bougies' => $bougies->count(),
            'bougies' => $bougies->map(fn($b) => [
                'id' => $b->id,
                'nom' => $b->nom,
                'ref' => $b->reference,
                'stock' => $b->quantite,
                'prix' => $b->prix,
                'collection' => $b->collection,
            ])
        ]);
    }
    
    /**
     * Ré-enregistrer les bougies de test
     */
    public function seedTestBougies(Request $request)
    {
        // Créer des bougies de test rapides
        Bougie::firstOrCreate(
            ['reference' => 'TEST-001'],
            [
                'parfum' => "Parfum naturel de cire d'abeille",
                'nom' => 'Bougie Test',
                'collection' => 'Nature',
                'format' => '200g',
                'type_cire' => "cire d'abeille 100% naturelle",
                'temps_brulure' => 40,
                'notes' => 'Bougie de test créée via API',
                'prix' => 25.00,
                'quantite' => 10,
                'seuil_alerte' => 5,
            ]
        );
        
        Bougie::firstOrCreate(
            ['reference' => 'TEST-002'],
            [
                'parfum' => "Parfum naturel de cire d'abeille",
                'nom' => 'Ganesh Test',
                'collection' => 'Spirit',
                'format' => 'sculpture',
                'type_cire' => "cire d'abeille 100% naturelle",
                'temps_brulure' => 60,
                'notes' => 'Test Ganesh',
                'prix' => 45.00,
                'quantite' => 5,
                'seuil_alerte' => 3,
            ]
        );
        
        return response()->json([
            'message' => '2 bougies de test créées',
            'total' => Bougie::count(),
        ]);
    }
}
