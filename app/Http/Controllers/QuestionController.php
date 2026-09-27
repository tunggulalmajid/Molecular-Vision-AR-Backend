<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $questionType = $request->input('question_type');

        $questions = Question::query()
            ->with(['category:id_category,name', 'options'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('id_category', $categoryId);
            })
            ->when($questionType, function ($query, $questionType) {
                $query->where('question_type', $questionType);
            })
            ->latest('id_question')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()
            ->select('id_category', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Questions/Index', [
            'questions' => $questions,
            'categories' => $categories,
            'filters' => [
                'search' => $search ?? '',
                'category_id' => $categoryId ?? '',
                'question_type' => $questionType ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(): Response
    {
        $categories = Category::query()
            ->select('id_category', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Questions/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validateQuestion($request);

        DB::transaction(function () use ($request) {
            /** @var Question $question */
            $question = Question::create([
                'name' => $request->input('name'),
                'id_category' => $request->input('id_category'),
                'question_type' => $request->input('question_type'),
            ]);

            $this->storeOptionsForQuestion($question, $request);
        });

        return redirect()
            ->route('questions.index')
            ->with('success', 'Butir soal kuis berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Question $question): Response
    {
        $question->load(['category:id_category,name', 'options']);

        $categories = Category::query()
            ->select('id_category', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Questions/Edit', [
            'question' => $question,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Question $question): RedirectResponse
    {
        $this->validateQuestion($request);

        DB::transaction(function () use ($request, $question) {
            $question->update([
                'name' => $request->input('name'),
                'id_category' => $request->input('id_category'),
                'question_type' => $request->input('question_type'),
            ]);

            // Clear old options and re-insert new ones
            $question->options()->forceDelete();

            $this->storeOptionsForQuestion($question, $request);
        });

        return redirect()
            ->route('questions.index')
            ->with('success', 'Butir soal kuis berhasil diperbarui.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()
            ->route('questions.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Validate request payload according to the question type.
     */
    protected function validateQuestion(Request $request): void
    {
        $request->validate([
            'name' => ['required', 'string'],
            'id_category' => ['required', 'integer', 'exists:categories,id_category'],
            'question_type' => ['required', 'in:multiple_choice,multiple_select,true_false,short_answer,matching'],
        ]);

        $type = $request->input('question_type');

        switch ($type) {
            case 'multiple_choice':
                $request->validate([
                    'options' => ['required', 'array', 'min:2'],
                    'options.*.option_text' => ['required', 'string'],
                    'options.*.is_correct' => ['required', 'boolean'],
                ]);

                /** @var list<array{option_text: string, is_correct: bool}> $options */
                $options = (array) $request->input('options', []);
                $correctCount = 0;
                foreach ($options as $opt) {
                    if (! empty($opt['is_correct'])) {
                        $correctCount++;
                    }
                }

                if ($correctCount !== 1) {
                    throw ValidationException::withMessages([
                        'options' => 'Pilihan ganda harus memiliki tepat 1 kunci jawaban yang benar.',
                    ]);
                }
                break;

            case 'multiple_select':
                $request->validate([
                    'options' => ['required', 'array', 'min:2'],
                    'options.*.option_text' => ['required', 'string'],
                    'options.*.is_correct' => ['required', 'boolean'],
                ]);

                /** @var list<array{option_text: string, is_correct: bool}> $options */
                $options = (array) $request->input('options', []);
                $correctCount = 0;
                foreach ($options as $opt) {
                    if (! empty($opt['is_correct'])) {
                        $correctCount++;
                    }
                }

                if ($correctCount < 1) {
                    throw ValidationException::withMessages([
                        'options' => 'Pilihan jamak (checkbox) harus memiliki minimal 1 kunci jawaban yang benar.',
                    ]);
                }
                break;

            case 'true_false':
                $request->validate([
                    'tf_answer' => ['required', 'in:Benar,Salah'],
                ]);
                break;

            case 'short_answer':
                $request->validate([
                    'numeric_answer' => ['required', 'numeric'],
                ], [
                    'numeric_answer.required' => 'Kunci jawaban angka wajib diisi.',
                    'numeric_answer.numeric' => 'Kunci jawaban isian singkat harus berupa angka.',
                ]);
                break;

            case 'matching':
                $request->validate([
                    'pairs' => ['required', 'array', 'min:2'],
                    'pairs.*.option_text' => ['required', 'string'],
                    'pairs.*.match_text' => ['required', 'string'],
                ], [
                    'pairs.min' => 'Soal menjodohkan harus memiliki minimal 2 pasang pernyataan.',
                    'pairs.*.option_text.required' => 'Pernyataan premis kiri wajib diisi.',
                    'pairs.*.match_text.required' => 'Jawaban pasangan kanan wajib diisi.',
                ]);
                break;
        }
    }

    /**
     * Store options for a question according to its type.
     */
    protected function storeOptionsForQuestion(Question $question, Request $request): void
    {
        $type = $question->question_type;

        switch ($type) {
            case 'multiple_choice':
            case 'multiple_select':
                /** @var list<array{option_text: string, is_correct: bool}> $options */
                $options = $request->input('options', []);
                foreach ($options as $opt) {
                    QuestionOption::create([
                        'id_question' => $question->id_question,
                        'option_text' => trim($opt['option_text']),
                        'match_text' => null,
                        'is_correct' => (bool) $opt['is_correct'],
                    ]);
                }
                break;

            case 'true_false':
                $tfAnswer = $request->input('tf_answer'); // 'Benar' or 'Salah'
                QuestionOption::create([
                    'id_question' => $question->id_question,
                    'option_text' => 'Benar',
                    'match_text' => null,
                    'is_correct' => $tfAnswer === 'Benar',
                ]);
                QuestionOption::create([
                    'id_question' => $question->id_question,
                    'option_text' => 'Salah',
                    'match_text' => null,
                    'is_correct' => $tfAnswer === 'Salah',
                ]);
                break;

            case 'short_answer':
                $numAnswer = (string) $request->input('numeric_answer');
                QuestionOption::create([
                    'id_question' => $question->id_question,
                    'option_text' => trim($numAnswer),
                    'match_text' => null,
                    'is_correct' => true,
                ]);
                break;

            case 'matching':
                /** @var list<array{option_text: string, match_text: string}> $pairs */
                $pairs = $request->input('pairs', []);
                foreach ($pairs as $pair) {
                    QuestionOption::create([
                        'id_question' => $question->id_question,
                        'option_text' => trim($pair['option_text']),
                        'match_text' => trim($pair['match_text']),
                        'is_correct' => true,
                    ]);
                }
                break;
        }
    }
}
