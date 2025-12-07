<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vinyle;

class VinyleSeeder extends Seeder
{
    public function run(): void
    {
        $vinyles = [

            // Boîte 1
            ['nom' => 'AC/DC',          'modele' => '', 'prix' => 27, 'quantite' => 36],
            ['nom' => 'Beatles',        'modele' => '', 'prix' => 27, 'quantite' => 3],
            ['nom' => 'Black Sabbath',  'modele' => '', 'prix' => 27, 'quantite' => 9],
            ['nom' => 'Blues Brothers', 'modele' => '', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'Bob Marley',     'modele' => '', 'prix' => 27, 'quantite' => 28],
            ['nom' => 'Depeche Mode',   'modele' => '', 'prix' => 27, 'quantite' => 10],

            // Boîte 2
            ['nom' => 'Michael Jackson',  'modele' => '', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'Linkin Park',      'modele' => '', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'Gorillaz',         'modele' => '', 'prix' => 27, 'quantite' => 19],
            ['nom' => 'JJ Goldman',       'modele' => '', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'Serge Gainsbourg', 'modele' => '', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'Eminem',           'modele' => '', 'prix' => 27, 'quantite' => 30],
            ['nom' => 'Daft Punk',        'modele' => '', 'prix' => 27, 'quantite' => 40],

            // Boîte 3
            ['nom' => 'Johnny Hallyday', 'modele' => '', 'prix' => 27, 'quantite' => 19],
            ['nom' => '2Pac',            'modele' => '', 'prix' => 27, 'quantite' => 31],
            ['nom' => 'Jul',             'modele' => '', 'prix' => 27, 'quantite' => 41],
            ['nom' => 'Renaud',          'modele' => '', 'prix' => 27, 'quantite' => 11],
            ['nom' => 'Queen',           'modele' => '', 'prix' => 27, 'quantite' => 28],

            // Boîte 4
            ['nom' => 'Harley',          'modele' => 'Guidon', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'Led Zeppelin',    'modele' => '',       'prix' => 27, 'quantite' => 10],
            ['nom' => 'Muse',            'modele' => '',       'prix' => 27, 'quantite' => 10],
            ['nom' => 'Ray Charles',     'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Guns N’ Roses',   'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Rammstein',       'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'PNL',             'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Dire Straits',    'modele' => '',       'prix' => 27, 'quantite' => 4],
            ['nom' => 'Shaka Ponk',      'modele' => '',       'prix' => 27, 'quantite' => 10],
            ['nom' => 'BTS',             'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Bad Bunny',       'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Arctic Monkeys',  'modele' => '',       'prix' => 27, 'quantite' => 6],
            ['nom' => 'Wu Tang',         'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Drake',           'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Orelsan',         'modele' => '',       'prix' => 27, 'quantite' => 6],
            ['nom' => 'Bruno Mars',      'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Radiohead',       'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'BB King',         'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Talking Heads',   'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Notorious BIG',   'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Coldplay',        'modele' => '',       'prix' => 27, 'quantite' => 5],
            ['nom' => 'Imagine Dragons', 'modele' => '',       'prix' => 27, 'quantite' => 5],

            // Boîte 5
            ['nom' => 'Metallica',     'modele' => '', 'prix' => 27, 'quantite' => 9],
            ['nom' => 'Lana Del Rey',  'modele' => '', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'U2',            'modele' => '', 'prix' => 27, 'quantite' => 19],
            ['nom' => 'Jimi Hendrix',  'modele' => '', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'Taylor Swift',  'modele' => '', 'prix' => 27, 'quantite' => 20],
            ['nom' => 'Billie Eilish', 'modele' => '', 'prix' => 27, 'quantite' => 20],
            ['nom' => 'IAM',           'modele' => '', 'prix' => 27, 'quantite' => 20],
            ['nom' => 'NTM',           'modele' => '', 'prix' => 27, 'quantite' => 23],
            ['nom' => 'Indochine',     'modele' => '', 'prix' => 27, 'quantite' => 10],

            // Boîte 6
            ['nom' => 'Elvis',                 'modele' => '',         'prix' => 27, 'quantite' => 10],
            ['nom' => 'Harry Styles',          'modele' => '',         'prix' => 27, 'quantite' => 15],
            ['nom' => 'Nirvana',               'modele' => '',         'prix' => 27, 'quantite' => 9],
            ['nom' => 'Pink Floyd',            'modele' => 'The Wall', 'prix' => 27, 'quantite' => 19],
            ['nom' => 'Pink Floyd',            'modele' => 'Dark Side','prix' => 27, 'quantite' => 19],
            ['nom' => 'Red Hot Chili Peppers', 'modele' => '',         'prix' => 27, 'quantite' => 10],
            ['nom' => 'Bruce Springsteen',     'modele' => '',         'prix' => 27, 'quantite' => 10],
            ['nom' => 'Stevie Wonder',         'modele' => '',         'prix' => 27, 'quantite' => 5],
            ['nom' => 'The Doors',             'modele' => '',         'prix' => 27, 'quantite' => 5],
            ['nom' => 'Kiss',                  'modele' => '',         'prix' => 27, 'quantite' => 10],
            ['nom' => 'The Weeknd',            'modele' => '',         'prix' => 27, 'quantite' => 9],
            ['nom' => 'Harley',                'modele' => 'Motor',    'prix' => 27, 'quantite' => 5],

            // Boîte 7
            ['nom' => 'Iron Maiden',   'modele' => '', 'prix' => 27, 'quantite' => 7],
            ['nom' => 'Prince',        'modele' => '', 'prix' => 27, 'quantite' => 8],
            ['nom' => 'Rolling Stones','modele' => '', 'prix' => 27, 'quantite' => 20],
            ['nom' => 'Mylène Farmer', 'modele' => '', 'prix' => 27, 'quantite' => 10],
            ['nom' => 'Bowie',         'modele' => '', 'prix' => 27, 'quantite' => 9],
            ['nom' => 'Snoop Dogg',    'modele' => '', 'prix' => 27, 'quantite' => 21],
        ];

        foreach ($vinyles as $data) {
            Vinyle::create($data);
        }
    }
}
