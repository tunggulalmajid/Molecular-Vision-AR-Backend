<?php

namespace Database\Seeders;

use App\Models\Degree;
use Illuminate\Database\Seeder;

class DegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $degrees = [
            'Kelas 10',
            'Kelas 11',
            'Kelas 12',
        ];

        foreach ($degrees as $name) {
            Degree::firstOrCreate(['name' => $name]);
        }
    }
}
