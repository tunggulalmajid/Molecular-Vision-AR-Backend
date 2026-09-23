<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $geometryCategory = Category::where('name', 'Geometri Molekul')->first();

        if (! $geometryCategory) {
            return;
        }

        $materials = [
            [
                'name' => 'Pengenalan Teori VSEPR',
                'id_category' => $geometryCategory->id_category,
                'description' => 'Memahami dasar-dasar teori Valence Shell Electron Pair Repulsion (VSEPR) dalam penentuan bentuk molekul.',
                'content' => 'Teori VSEPR menyatakan bahwa pasangan elektron valensi di sekitar atom pusat akan saling tolak-menolak sejauh mungkin untuk meminimalkan gaya tolak antar elektron...',
            ],
            [
                'name' => 'Bentuk Dasar Geometri Molekul',
                'id_category' => $geometryCategory->id_category,
                'description' => 'Mengenal 5 bentuk geometri molekul dasar: Linear, Trigonal Planar, Tetrahedral, Trigonal Bipiramidal, dan Oktahedral.',
                'content' => 'Berdasarkan domain elektron ikatan (DEI) dan domain elektron bebas (DEB), bentuk dasar molekul dapat diklasifikasikan menjadi lima susunan geometri ruang utama...',
            ],
        ];

        foreach ($materials as $material) {
            Material::firstOrCreate(
                ['name' => $material['name'], 'id_category' => $material['id_category']],
                $material,
            );
        }
    }
}
