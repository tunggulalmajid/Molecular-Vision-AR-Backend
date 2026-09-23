<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
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

        // 1. Multiple Choice Question
        $q1 = Question::firstOrCreate(
            ['name' => 'Berapakah sudut ikatan yang terbentuk pada molekul air (H2O)?', 'id_category' => $geometryCategory->id_category],
            ['question_type' => 'multiple_choice'],
        );

        $optionsQ1 = [
            ['option_text' => '180°', 'is_correct' => false],
            ['option_text' => '109.5°', 'is_correct' => false],
            ['option_text' => '104.5°', 'is_correct' => true],
            ['option_text' => '120°', 'is_correct' => false],
        ];

        foreach ($optionsQ1 as $opt) {
            QuestionOption::firstOrCreate(
                ['id_question' => $q1->id_question, 'option_text' => $opt['option_text']],
                $opt,
            );
        }

        // 2. True / False Question
        $q2 = Question::firstOrCreate(
            ['name' => 'Molekul karbon dioksida (CO2) memiliki bentuk geometri linear dan bersifat nonpolar.', 'id_category' => $geometryCategory->id_category],
            ['question_type' => 'true_false'],
        );

        $optionsQ2 = [
            ['option_text' => 'Benar', 'is_correct' => true],
            ['option_text' => 'Salah', 'is_correct' => false],
        ];

        foreach ($optionsQ2 as $opt) {
            QuestionOption::firstOrCreate(
                ['id_question' => $q2->id_question, 'option_text' => $opt['option_text']],
                $opt,
            );
        }
    }
}
