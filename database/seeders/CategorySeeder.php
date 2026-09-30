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
                'order' => 1,
                'description' => 'Mempelajari susunan ruang tiga dimensi dari atom-atom dalam molekul berdasarkan teori VSEPR.',
            ],
            [
                'name' => 'Ikatan Kimia',
                'order' => 2,
                'description' => 'Mempelajari jenis-jenis ikatan kimia seperti kovalen, polar, nonpolar, dan ikatan ionik.',
            ],
            [
                'name' => 'Hibridisasi Orbital',
                'order' => 3,
                'description' => 'Mempelajari konsep pencampuran orbital atom untuk membentuk orbital hibrida baru (sp, sp2, sp3, sp3d, sp3d2).',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'order' => $category['order'],
                    'description' => $category['description'],
                ],
            );
        }
    }
}
