<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bougie;
use App\Models\StockAlert;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\Storage;

class BougieSeeder extends Seeder
{
    public function run(): void
    {
        // Créer le dossier pour les images si nécessaire
        if (!Storage::disk('public')->exists('bougies')) {
            Storage::disk('public')->makeDirectory('bougies');
        }

        // === SCULPTURES SPIRITUELLES ===
        
        // Ganesh - Pièce maîtresse
        $ganesh = Bougie::create([
            'reference' => 'BOUG-GNSH-001',
            'slug' => 'ganesh-sculpture-cire-abeille',
            'parfum' => 'Santal & Vanille',
            'nom' => 'Ganesh - Sculpture spirituelle',
            'image' => 'ganesh-sculpture.jpg',
            'collection' => 'Spirituel',
            'format' => 'sculpture',
            'dimensions' => '8×5×12 cm',
            'poids' => 320,
            'type_cire' => "cire d'abeille",
            'technique' => 'moulage précision',
            'numero_serie' => '3/10',
            'temps_brulure' => 60,
            'notes' => "Notes créateurs: santal sacré, vanille douce, cire purifiée. Éléphant: force, sagesse, protection.",
            'description' => "Ganesh, porte-bonheur. Moulage artisanal sur cire d'abeille 100% naturelle. Chaque sculpture est unique, avec ses subtiles variations qui témoignent du travail fait-main. Présenté dans son temple miniature.",
            'prix' => 45.00,
            'quantite' => 3,
            'seuil_alerte' => 3,
        ]);

        // Lotus - Fleur spirituelle
        $lotus = Bougie::create([
            'reference' => 'BOUG-LOTUS-001',
            'slug' => 'lotus-fleur-cire-abeille',
            'parfum' => 'Néroli & Jasmin',
            'nom' => 'Lotus - Fleur Épanouie',
            'image' => 'lotus-sculpture.jpg',
            'collection' => 'Art',
            'format' => 'sculpture',
            'dimensions' => 'Ø10×6 cm',
            'poids' => 280,
            'type_cire' => "cire d'abeille",
            'technique' => 'moulage précision',
            'numero_serie' => '5/20',
            'temps_brulure' => 55,
            'notes' => "Néroli délicat, jasmin sensuel. Symbolisme: pureté, renaissance, spiritualité.",
            'description' => "Fleur de lotus en pleine éclosion, sculptée dans la cire d'abeille dorée. Chaque pétale est moulé individuellement puis assemblé à la main. Série limitée de 20 exemplaires. Symbole de pureté et d'éveil spirituel.",
            'prix' => 38.00,
            'quantite' => 5,
            'seuil_alerte' => 3,
        ]);

        // Chat élégant - Sculpture art
        $chat = Bougie::create([
            'reference' => 'BOUG-CHAT-001',
            'slug' => 'chat-sculpture-cire-abeille',
            'parfum' => "Fleur d'oranger",
            'nom' => 'Chat Élégant - Sculpture Art',
            'image' => 'chat-sculpture.jpg',
            'collection' => 'Art',
            'format' => 'sculpture',
            'dimensions' => '5×4×8 cm',
            'poids' => 180,
            'type_cire' => "cire d'abeille",
            'technique' => 'moulage artistique',
            'numero_serie' => 'Unique',
            'temps_brulure' => 35,
            'notes' => "Note fraîche et légère, parfait pour une ambiance douce.",
            'description' => "Silhouette féline épurée, inspirée de l'art égyptien. Formes minimalistes, lignes pures. Cette pièce unique joue sur la lumière dorée de la cire d'abeille et l'ombre de son profil sculptural.",
            'prix' => 28.00,
            'quantite' => 1,
            'seuil_alerte' => 2,
        ]);

        // Chandelle cylindrique (classique)
        $chandelle = Bougie::create([
            'reference' => 'BOUG-CHND-001',
            'slug' => 'chandelle-cire-abeille-naturelle',
            'parfum' => 'Neutre (miel naturel)',
            'nom' => 'Chandelle Naturelle',
            'image' => 'chandelle-doree.jpg',
            'collection' => 'Nature',
            'format' => 'chandelle',
            'dimensions' => 'Ø4×15 cm',
            'poids' => 250,
            'type_cire' => "cire d'abeille",
            'technique' => 'coulée à la main',
            'numero_serie' => null,
            'temps_brulure' => 45,
            'notes' => "Parfum subtil de cire d'abeille naturelle, sans ajout.",
            'description' => "Chandelle aux bords irréguliers, façonnée à la main. Cire d'abeille purifiée, mèche coton sans plomb. Éclairage chaud, doré, qui crée une ambiance feutrée et naturelle.",
            'prix' => 18.00,
            'quantite' => 12,
            'seuil_alerte' => 5,
        ]);

        // === BOUGIES VOTIVES ===
        
        Bougie::create([
            'reference' => 'BOUG-VOT-RSE-001',
            'slug' => 'votive-rose-damas',
            'parfum' => 'Rose Damas',
            'nom' => 'Votive Rose Damas',
            'image' => 'votive-rose.jpg',
            'collection' => 'Classique',
            'format' => 'votive',
            'dimensions' => 'Ø5×6 cm',
            'poids' => 120,
            'type_cire' => 'cire de soja',
            'technique' => 'coulée',
            'numero_serie' => null,
            'temps_brulure' => 20,
            'notes' => "Rose authentique, riche et florale.",
            'description' => "Bougie votive parfumée à la rose Damas. Cire de soja végétale, mèche en bois crackling. Pour créer un coin doux et fleuri.",
            'prix' => 14.00,
            'quantite' => 25,
            'seuil_alerte' => 8,
        ]);

        Bougie::create([
            'reference' => 'BOUG-VOT-LVD-001',
            'slug' => 'votive-lavande-provence',
            'parfum' => "Lavande de Provence",
            'nom' => 'Votive Lavande',
            'image' => 'votive-lavande.jpg',
            'collection' => 'Nature',
            'format' => 'votive',
            'dimensions' => 'Ø5×6 cm',
            'poids' => 120,
            'type_cire' => 'cire de soja',
            'technique' => 'coulée',
            'numero_serie' => null,
            'temps_brulure' => 20,
            'notes' => "Lavande apaisante, récoltée en Provence.",
            'description' => "L'essence de la Provence dans une bougie. Notes herbacées et relaxantes, parfait pour la salle de bain ou la chambre.",
            'prix' => 14.00,
            'quantite' => 20,
            'seuil_alerte' => 8,
        ]);

        // === COLLECTION SPIRITUELLE ===
        
        Bougie::create([
            'reference' => 'BOUG-SPR-CED-001',
            'slug' => 'fondue-cedre-sage',
            'parfum' => 'Cèdre & Sauge',
            'nom' => 'Fondue Cèdre & Sauge',
            'image' => 'fondue-cedre.jpg',
            'collection' => 'Spirituel',
            'format' => '200g',
            'dimensions' => 'Pot Ø8×9 cm',
            'poids' => 200,
            'type_cire' => 'cire de soja',
            'technique' => 'coulée',
            'numero_serie' => null,
            'temps_brulure' => 40,
            'notes' => "Boisé profond, herbacé purifiant. Pour la méditation.",
            'description' => "Bougie de rituel. Cèdre forestier et sauge blanche réunis pour créer une ambiance de purification. Idéale pour accompagner la méditation ou les moments de recueillement.",
            'prix' => 32.00,
            'quantite' => 8,
            'seuil_alerte' => 4,
        ]);

        // === COLLECTION ART/SAISONNIÈRE ===
        
        Bougie::create([
            'reference' => 'BOUG-CND-PLR-001',
            'slug' => 'chandelle-polaire-en-pot',
            'parfum' => "Pin & Eucalyptus",
            'nom' => 'Chandelle Polaire',
            'image' => 'chandelle-polaire.jpg',
            'collection' => 'Saisonniere',
            'format' => '300g',
            'dimensions' => 'Pot Ø9×10 cm',
            'poids' => 300,
            'type_cire' => 'cire de colza',
            'technique' => 'coulée',
            'numero_serie' => null,
            'temps_brulure' => 55,
            'notes' => "Forêt enneigée, fraîcheur cristalline.",
            'description' => "Collection hivernale. Souffle de montagne, air pur et résineux. Cire de colza européenne, combustion propre et longue.",
            'prix' => 38.00,
            'quantite' => 15,
            'seuil_alerte' => 5,
        ]);

        Bougie::create([
            'reference' => 'BOUG-SCL-NST-001',
            'slug' => 'sculpture-nest-miniature',
            'parfum' => 'Ambre & Musc',
            'nom' => 'Nest - Sculpture nature',
            'image' => 'nest-sculpture.jpg',
            'collection' => 'Art',
            'format' => 'sculpture',
            'dimensions' => '10×7×6 cm',
            'poids' => 220,
            'type_cire' => "cire d'abeille",
            'technique' => 'modelage main',
            'numero_serie' => '7/15',
            'temps_brulure' => 50,
            'notes' => "Ambre chaud, musc subtil. Évocation d'un nid.",
            'description' => "Sculpture organique inspirée des nids d'oiseaux. Chaque pièce est modelée à la main, créant des textures uniques. Série limitée de 15 exemplaires.",
            'prix' => 42.00,
            'quantite' => 2,
            'seuil_alerte' => 2,
        ]);

        $this->command->info('8 bougies créées avec succès');
        $this->command->info('Dont : Ganesh (45€), Chat (28€), Chandelle (18€)');
    }
}
