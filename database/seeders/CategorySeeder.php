<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Geometri Molekul',
                'description' => 'Mempelajari susunan ruang tiga dimensi dari atom-atom dalam molekul berdasarkan teori VSEPR.',
            ],
            [
                'name' => 'Ikatan Kimia',
                'description' => 'Mempelajari jenis-jenis ikatan kimia seperti kovalen, polar, nonpolar, dan ikatan ionik.',
            ],
            [
                'name' => 'Hibridisasi Orbital',
                'description' => 'Mempelajari konsep pencampuran orbital atom untuk membentuk orbital hibrida baru (sp, sp2, sp3, sp3d, sp3d2).',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']],
            );
        }
    }
}
