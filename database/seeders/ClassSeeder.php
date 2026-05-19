<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('classes')->insert([
            // Media vormgeving
            ['name' => 'MV2A', 'class_group' => 'Media vormgeving', 'LOB' => 'Sandra Mascini, Steven Norbart', 'total_amount' => 23, 'shared_total' => null],
            ['name' => 'MV2B', 'class_group' => 'Media vormgeving', 'LOB' => 'Petra Heebink',                  'total_amount' => 16, 'shared_total' => null],
            ['name' => 'MV2C', 'class_group' => 'Media vormgeving', 'LOB' => 'Gert Bosman',                    'total_amount' => 21, 'shared_total' => null],

            // Interieuradviseur
            ['name' => 'BOW3B', 'class_group' => 'Interieuradviseur', 'LOB' => 'Alwin Lindenholz',                                                    'total_amount' => 2,  'shared_total' => null],
            ['name' => 'BOW3C', 'class_group' => 'Interieuradviseur', 'LOB' => 'Lineke Benjamins',                                                     'total_amount' => 1,  'shared_total' => null],
            ['name' => 'BOW3D', 'class_group' => 'Interieuradviseur', 'LOB' => 'Eduard Dijkhuizen',                                                    'total_amount' => 1,  'shared_total' => null],
            ['name' => 'BOW2A', 'class_group' => 'Interieuradviseur', 'LOB' => 'Ilse Kroon, Mirjam Verweij',                                           'total_amount' => 24, 'shared_total' => null],
            ['name' => 'BOW2B', 'class_group' => 'Interieuradviseur', 'LOB' => 'Lineke Benjamins, Cornalijn Overweg',                                  'total_amount' => 20, 'shared_total' => null],
            ['name' => 'BOW2V', 'class_group' => 'Interieuradviseur', 'LOB' => 'Ilse Kroon',                                                           'total_amount' => 1,  'shared_total' => null],
            ['name' => 'BOW2C', 'class_group' => 'Interieuradviseur', 'LOB' => 'Iteke de Jong, Diana Dunnink',                                         'total_amount' => 23,  'shared_total' => null],

            // Podium- en Evenemententechniek
            ['name' => 'P2.1', 'class_group' => 'Podium- en Evenemententechniek', 'LOB' => 'Aurelia de Vries, Henk Mekkring', 'total_amount' => 33, 'shared_total' => 'podium'],
            ['name' => 'P2.2', 'class_group' => 'Podium- en Evenemententechniek', 'LOB' => 'Aurelia de Vries, Henk Mekkring', 'total_amount' => 33, 'shared_total' => 'podium'],

            // Muzikant
            ['name' => 'AR2MZ', 'class_group' => 'Muzikant', 'LOB' => 'Freek de Vos, René Post', 'total_amount' => 17, 'shared_total' => null],

            // Acteur
            ['name' => 'AR2AC', 'class_group' => 'Acteur', 'LOB' => 'Nelleke Simons', 'total_amount' => 10, 'shared_total' => null],

            // Expert IT systems and devices
            ['name' => 'IT2A', 'class_group' => 'Expert IT systems and devices', 'LOB' => 'Dennis Gerding', 'total_amount' => 22, 'shared_total' => null],

            // Software developer
            ['name' => 'SD2A', 'class_group' => 'Software developer', 'LOB' => 'Koos Starreveld', 'total_amount' => 24, 'shared_total' => null],

            // AV-productie
            ['name' => 'AV3B', 'class_group' => 'AV-productie', 'LOB' => 'Andre van der Stouwe', 'total_amount' => 19, 'shared_total' => null],

            // Fotograaf
            ['name' => 'AV3A', 'class_group' => 'Fotograaf', 'LOB' => 'Jorgen Pal', 'total_amount' => 10, 'shared_total' => null],

            // Mode
            ['name' => '2FR',  'class_group' => 'Mode', 'LOB' => 'Katja Borger', 'total_amount' => 5,  'shared_total' => null],
            ['name' => '2FC',  'class_group' => 'Mode', 'LOB' => 'Marjet Amptmeijer', 'total_amount' => 7,  'shared_total' => null],
            ['name' => '2AFT', 'class_group' => 'Mode', 'LOB' => 'Stefan Breuer', 'total_amount' => 11, 'shared_total' => 'mode_aft_ft'],
            ['name' => '2FT',  'class_group' => 'Mode', 'LOB' => 'Stefan Breuer', 'total_amount' => 11, 'shared_total' => 'mode_aft_ft'],
            ['name' => '2FDA', 'class_group' => 'Mode', 'LOB' => 'Katja Borger', 'total_amount' => 34, 'shared_total' => 'mode_fda_fdb'],
            ['name' => '2FDB', 'class_group' => 'Mode', 'LOB' => 'Marjet Amptmeijer', 'total_amount' => 34, 'shared_total' => 'mode_fda_fdb'],

            // Creative Development
            ['name' => 'CD2A', 'class_group' => 'Creative Development', 'LOB' => 'Rhonja Gieles', 'total_amount' => 35, 'shared_total' => 'cd'],
            ['name' => 'CD2B', 'class_group' => 'Creative Development', 'LOB' => 'Rhonja Gieles', 'total_amount' => 35, 'shared_total' => 'cd'],
        ]);
    }
}
