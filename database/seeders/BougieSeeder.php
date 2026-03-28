<?php

namespace Database\Seeders;

<<<<<<< HEAD
use Illuminate\Database\Seeder;
use App\Models\Bougie;

class BougieSeeder extends Seeder
{
    public function run(): void
    {
        // === SIGNATURE CIRE D'ABEILLE ===
        // Toutes en cire d'abeille 100% naturelle, sans parfum ajouté
        
        // Ganesh - Sculpture sacrée
        Bougie::create([
            'reference' => 'BOUG-GNSH-001',
            'parfum' => "Parfum naturel de cire d'abeille",
            'nom' => 'Ganesh',
            'collection' => 'Spirit',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => 60,
            'notes' => "Sculpture sacrée coulée à la main. Cire d'abeille pure de nos ruchers locaux, sans parfum ajouté ni colorant. La flamme naturelle dégage un doux parfum de miel qui apaise l'esprit.",
            'prix' => 45.00,
            'quantite' => 3,
            'seuil_alerte' => 3,
        ]);

        // Lotus - Pureté naturelle
        Bougie::create([
            'reference' => 'BOUG-LOTUS-001',
            'parfum' => "Parfum naturel de cire d'abeille",
            'nom' => 'Lotus',
            'collection' => 'Spirit',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => 55,
            'notes' => "Fleur de lotus sculptée dans la cire dorée des abeilles. Artisanale, sans additif chimique. La combustion propre purifie l'air de votre intérieur.",
            'prix' => 42.00,
            'quantite' => 5,
            'seuil_alerte' => 3,
        ]);

        // Chat élégant - Art animalier
        Bougie::create([
            'reference' => 'BOUG-CHAT-001',
            'parfum' => "Parfum naturel de cire d'abeille",
            'nom' => 'Le Chat',
            'collection' => 'Art',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => 35,
            'notes' => "Silhouette féline façonnée à la main. Cire brute non raffinée, issue d'apiculture locale et respectueuse. Une lueur chaleureuse pour vos soirées.",
            'prix' => 32.00,
            'quantite' => 8,
            'seuil_alerte' => 4,
        ]);

        // La Chandelle - Classique intemporel
        Bougie::create([
            'reference' => 'BOUG-CHND-001',
            'parfum' => "Parfum naturel de cire d'abeille",
            'nom' => 'La Chandelle',
            'collection' => 'Nature',
            'format' => 'chandelle',
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => 45,
            'notes' => "La tradition revisitée. Cire d'abeille brute aux bienfaits connus depuis l'Antiquité : combustion longue, sans fumée ni suie. Parfait pour les repas en famille ou la méditation.",
            'prix' => 22.00,
            'quantite' => 25,
            'seuil_alerte' => 8,
        ]);

        // === BOUGIES VOTIVES ===
        
        Bougie::create([
            'reference' => 'BOUG-VOT-RSE-001',
            'parfum' => "Parfum naturel de cire d'abeille",
            'nom' => 'Votive Douceur',
            'collection' => 'Nature',
            'format' => 'votive',
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => 20,
            'notes' => "Petite bougie votive en cire d'abeille pure. Pour créer une ambiance chaleureuse sans compromis sur la qualité. Sans parfum de synthèse ni substance toxique.",
            'prix' => 16.00,
            'quantite' => 40,
            'seuil_alerte' => 12,
        ]);

        // La Ruche - Forme emblématique
        Bougie::create([
            'reference' => 'BOUG-RUCH-001',
            'parfum' => "Parfum naturel de cire d'abeille",
            'nom' => 'La Ruche',
            'collection' => 'Nature',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => 50,
            'notes' => "Hommage aux demoiselles butineuses. Motif alvéolé sculpté à la main dans notre cire d'abeille la plus pure. Chaque bougie est unique, façonnée par Séraphie dans son atelier.",
            'prix' => 28.00,
            'quantite' => 6,
            'seuil_alerte' => 3,
        ]);

        // === COLLECTION ART/SAISONNIÈRE ===
        
        Bougie::create([
            'reference' => 'BOUG-SCL-NST-001',
            'parfum' => "Parfum naturel de cire d'abeille",
            'nom' => 'Nest',
            'collection' => 'Art',
            'format' => 'sculpture',
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => 50,
            'notes' => "Sculpture inspirée des nids d'oiseaux. Cire d'abeille locale brute, non blanchie, conservant tout son parfum naturel de miel et ses propriétés purifiantes.",
            'prix' => 38.00,
            'quantite' => 4,
            'seuil_alerte' => 2,
        ]);

        // Fondue Hiver - Édition limitée
        Bougie::create([
            'reference' => 'BOUG-SPR-CED-001',
            'parfum' => "Parfum naturel de cire d'abeille",
            'nom' => 'Fondue Étoilée',
            'collection' => 'Spirit',
            'format' => '200g',
            'type_cire' => "cire d'abeille 100% naturelle",
            'temps_brulure' => 45,
            'notes' => "Fondue traditionnelle revisitée, coulée à la main dans de la cire d'abeille dorée. Pour une ambiance douce et authentique pendant les longues soirées d'hiver.",
            'prix' => 35.00,
            'quantite' => 12,
            'seuil_alerte' => 5,
        ]);

        $this->command->info('8 bougies créées avec succès');
        $this->command->info('Toutes en cire d\'abeille 100% naturelle, sans parfum de synthèse');
=======
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BougieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Bougie::factory()->count(10)->create();
>>>>>>> origin/master
    }
}
