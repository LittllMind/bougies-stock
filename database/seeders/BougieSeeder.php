<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bougie;

class BougieSeeder extends Seeder
{
    public function run(): void
    {
        // === SCULPTURES SPIRITUELLES ===
        
        // Ganesh - Pièce maîtresse
        Bougie::create([
            'reference' => 'BOUG-GNSH-001',
            'parfum' => 'Santal & Vanille',
            'nom' => 'Ganesh',
            'collection' => 'Spirit',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille",
            'temps_brulure' => 60,
            'notes' => "Santal sacré, vanille douce. Symbole de force et protection.",
            'prix' => 45.00,
            'quantite' => 3,
            'seuil_alerte' => 3,
        ]);

        // Lotus - Fleur spirituelle
        Bougie::create([
            'reference' => 'BOUG-LOTUS-001',
            'parfum' => 'Néroli & Jasmin',
            'nom' => 'Lotus',
            'collection' => 'Spirit',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille",
            'temps_brulure' => 55,
            'notes' => "Néroli délicat, jasmin sensuel. Symbolisme pureté.",
            'prix' => 38.00,
            'quantite' => 5,
            'seuil_alerte' => 3,
        ]);

        // Chat élégant - Sculpture art
        Bougie::create([
            'reference' => 'BOUG-CHAT-001',
            'parfum' => "Fleur d'oranger",
            'nom' => 'Chat',
            'collection' => 'Art',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille",
            'temps_brulure' => 35,
            'notes' => "Note fraîche et légère, ambiance douce.",
            'prix' => 28.00,
            'quantite' => 1,
            'seuil_alerte' => 2,
        ]);

        // Chandelle cylindrique (classique)
        Bougie::create([
            'reference' => 'BOUG-CHND-001',
            'parfum' => 'Neutre',
            'nom' => 'Chandelle',
            'collection' => 'Nature',
            'format' => 'chandelle',
            'type_cire' => "cire d'abeille",
            'temps_brulure' => 45,
            'notes' => "Parfum subtil de cire d'abeille naturelle.",
            'prix' => 18.00,
            'quantite' => 12,
            'seuil_alerte' => 5,
        ]);

        // === BOUGIES VOTIVES ===
        
        Bougie::create([
            'reference' => 'BOUG-VOT-RSE-001',
            'parfum' => 'Rose Damas',
            'nom' => 'Votive Rose Damas',
            'collection' => 'Classique',
            'format' => 'votive',
            'type_cire' => 'cire de soja',
            'temps_brulure' => 20,
            'notes' => "Rose authentique, riche et florale.",
            'prix' => 14.00,
            'quantite' => 25,
            'seuil_alerte' => 8,
        ]);

        Bougie::create([
            'reference' => 'BOUG-VOT-LVD-001',
            'parfum' => "Lavande de Provence",
            'nom' => 'Votive Lavande',
            'collection' => 'Nature',
            'format' => 'votive',
            'type_cire' => 'cire de soja',
            'temps_brulure' => 20,
            'notes' => "Lavande apaisante, récoltée en Provence.",
            'prix' => 14.00,
            'quantite' => 20,
            'seuil_alerte' => 8,
        ]);

        // === COLLECTION SPIRITUELLE ===
        
        Bougie::create([
            'reference' => 'BOUG-SPR-CED-001',
            'parfum' => 'Cèdre & Sauge',
            'nom' => 'Fondue Cèdre',
            'collection' => 'Spirit',
            'format' => '200g',
            'type_cire' => 'cire de soja',
            'temps_brulure' => 40,
            'notes' => "Boisé profond, herbacé purifiant.",
            'prix' => 32.00,
            'quantite' => 8,
            'seuil_alerte' => 4,
        ]);

        // === COLLECTION ART/SAISONNIÈRE ===
        
        Bougie::create([
            'reference' => 'BOUG-CND-PLR-001',
            'parfum' => "Pin & Eucalyptus",
            'nom' => 'Chandelle Polaire',
            'collection' => 'Saisonniere',
            'format' => '300g',
            'type_cire' => 'cire de colza',
            'temps_brulure' => 55,
            'notes' => "Forêt enneigée, fraîcheur cristalline.",
            'prix' => 38.00,
            'quantite' => 15,
            'seuil_alerte' => 5,
        ]);

        Bougie::create([
            'reference' => 'BOUG-SCL-NST-001',
            'parfum' => 'Ambre & Musc',
            'nom' => 'Nest',
            'collection' => 'Art',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille",
            'temps_brulure' => 50,
            'notes' => "Ambre chaud, musc subtil. Évocation d'un nid.",
            'prix' => 42.00,
            'quantite' => 2,
            'seuil_alerte' => 2,
        ]);

        $this->command->info('8 bougies créées avec succès');
        $this->command->info('Dont : Ganesh (45€), Chat (28€), Chandelle (18€)');
    }
}
