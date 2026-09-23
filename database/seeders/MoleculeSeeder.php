<?php

namespace Database\Seeders;

use App\Models\Molecule;
use Illuminate\Database\Seeder;

class MoleculeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $molecules = [
            [
                'name' => 'Air',
                'model_3d_url' => 'https://assets.mvar.id/models/h2o.glb',
                'formula' => 'H2O',
                'shape' => 'Bengkok (Bent)',
                'bent' => '104.5°',
                'bond_type' => 'Kovalen Polar',
                'description' => 'Molekul air tersusun dari dua atom hidrogen yang terikat secara kovalen pada satu atom oksigen dengan dua pasangan elektron bebas pada atom pusat.',
            ],
            [
                'name' => 'Karbon Dioksida',
                'model_3d_url' => 'https://assets.mvar.id/models/co2.glb',
                'formula' => 'CO2',
                'shape' => 'Linear',
                'bent' => '180°',
                'bond_type' => 'Kovalen Nonpolar',
                'description' => 'Molekul linear yang terdiri dari atom karbon yang berikatan rangkap dua dengan dua atom oksigen.',
            ],
            [
                'name' => 'Metana',
                'model_3d_url' => 'https://assets.mvar.id/models/ch4.glb',
                'formula' => 'CH4',
                'shape' => 'Tetrahedral',
                'bent' => '109.5°',
                'bond_type' => 'Kovalen Nonpolar',
                'description' => 'Hidrokarbon paling sederhana dengan atom karbon di pusat berikatan kovalen tunggal dengan empat atom hidrogen.',
            ],
            [
                'name' => 'Amonia',
                'model_3d_url' => 'https://assets.mvar.id/models/nh3.glb',
                'formula' => 'NH3',
                'shape' => 'Trigonal Piramidal',
                'bent' => '107°',
                'bond_type' => 'Kovalen Polar',
                'description' => 'Molekul dengan satu atom nitrogen terikat pada tiga atom hidrogen dengan satu pasangan elektron bebas.',
            ],
        ];

        foreach ($molecules as $molecule) {
            Molecule::firstOrCreate(
                ['formula' => $molecule['formula']],
                $molecule,
            );
        }
    }
}
