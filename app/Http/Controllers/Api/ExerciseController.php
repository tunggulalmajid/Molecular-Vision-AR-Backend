<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\UserCategoryProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OA;

class ExerciseController extends Controller
{
    /**
     * Display a listing of categories for exercises with question counts.
     */
    #[OA\Get(
        path: '/exercises',
        summary: 'Katalog Kategori Latihan Soal',
        description: 'Mengambil seluruh kategori soal latihan beserta jumlah butir soal yang tersedia pada masing-masing kategori.',
        tags: ['Latihan & Soal (Exercise)'],
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar kategori latihan soal berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Daftar kategori latihan soal berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Geometri Molekul'),
                                    new OA\Property(property: 'description', type: 'string', example: 'Latihan pemahaman bentuk geometri molekul berdasarkan teori domain elektron.'),
                                    new OA\Property(property: 'questions_count', type: 'integer', example: 5),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->withCount('questions')
            ->orderBy('id_category')
            ->get(['id_category', 'name', 'description']);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori latihan soal berhasil diambil.',
            'data' => $categories,
        ]);
    }

    /**
     * Get quiz questions for a category (with answers hidden for anti-cheat).
     */
    #[OA\Get(
        path: '/exercises/{id_category}',
        summary: 'Ambil Butir Soal Kuis (Anti-Cheat)',
        description: 'Mengambil daftar butir soal latihan untuk kategori tertentu. Seluruh kunci jawaban (is_correct) disembunyikan agar tidak terjadi kebocoran kunci jawaban ke client mobile.',
        tags: ['Latihan & Soal (Exercise)'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'id_category',
                in: 'path',
                description: 'ID kategori kuis',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar butir soal kuis berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Daftar butir soal kuis berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'category',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Geometri Molekul'),
                                        new OA\Property(property: 'description', type: 'string', example: 'Latihan pemahaman bentuk geometri molekul.'),
                                        new OA\Property(property: 'total_questions', type: 'integer', example: 5),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'questions',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id_question', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'Berapakah sudut ikatan pada molekul air (H2O)?'),
                                            new OA\Property(property: 'question_type', type: 'string', example: 'multiple_choice'),
                                            new OA\Property(
                                                property: 'options',
                                                type: 'array',
                                                items: new OA\Items(
                                                    properties: [
                                                        new OA\Property(property: 'id_option', type: 'integer', example: 10),
                                                        new OA\Property(property: 'option_text', type: 'string', example: '104.5°'),
                                                    ]
                                                )
                                            ),
                                            new OA\Property(
                                                property: 'matching_targets',
                                                type: 'array',
                                                items: new OA\Items(type: 'string'),
                                                description: 'Daftar target jodoh acak khusus untuk tipe matching',
                                                nullable: true
                                            ),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Kategori kuis tidak ditemukan.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Kategori kuis tidak ditemukan.'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function show(int $id_category): JsonResponse
    {
        $category = Category::find($id_category);

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori kuis tidak ditemukan.',
            ], 404);
        }

        $questions = Question::query()
            ->where('id_category', $id_category)
            ->with(['options' => function ($q) {
                $q->orderBy('id_option');
            }])
            ->orderBy('id_question')
            ->get();

        $formattedQuestions = $questions->map(function (Question $q) {
            $options = [];
            $matchingTargets = null;

            switch ($q->question_type) {
                case 'multiple_choice':
                case 'multiple_select':
                    $options = $q->options->map(fn (QuestionOption $opt) => [
                        'id_option' => $opt->id_option,
                        'option_text' => $opt->option_text,
                    ])->values()->all();
                    break;

                case 'true_false':
                    $options = [
                        ['option_text' => 'Benar'],
                        ['option_text' => 'Salah'],
                    ];
                    break;

                case 'short_answer':
                    $options = [];
                    break;

                case 'matching':
                    $options = $q->options->map(fn (QuestionOption $opt) => [
                        'id_option' => $opt->id_option,
                        'option_text' => $opt->option_text,
                    ])->values()->all();

                    // Acak pasangan kanan untuk keperluan antarmuka drag-and-drop mobile
                    $matchingTargets = $q->options
                        ->pluck('match_text')
                        ->filter()
                        ->shuffle()
                        ->values()
                        ->all();
                    break;
            }

            return [
                'id_question' => $q->id_question,
                'name' => $q->name,
                'question_type' => $q->question_type,
                'options' => $options,
                'matching_targets' => $matchingTargets,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar butir soal kuis berhasil diambil.',
            'data' => [
                'category' => [
                    'id_category' => $category->id_category,
                    'name' => $category->name,
                    'description' => $category->description,
                    'total_questions' => $questions->count(),
                ],
                'questions' => $formattedQuestions,
            ],
        ]);
    }

    /**
     * Submit answers for a quiz category, auto-grade, record exercises, and update category progress.
     */
    #[OA\Post(
        path: '/exercises/{id_category}',
        summary: 'Submit Jawaban Kuis & Auto-Grading',
        description: 'Mengirimkan seluruh jawaban siswa untuk kategori tersebut. Server melakukan evaluasi otomatis (grading) untuk ke-5 tipe soal, menyimpan hasil latihan, dan otomatis memperbarui progres kategori (UserCategoryProgress).',
        tags: ['Latihan & Soal (Exercise)'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'id_category',
                in: 'path',
                description: 'ID kategori kuis',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['answers'],
                properties: [
                    new OA\Property(
                        property: 'answers',
                        type: 'array',
                        items: new OA\Items(
                            required: ['id_question', 'answer'],
                            properties: [
                                new OA\Property(property: 'id_question', type: 'integer', example: 1),
                                new OA\Property(property: 'answer', description: 'Jawaban user (ID opsi, teks isian, atau array pasangan/pilihan)', example: '10'),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Latihan soal berhasil dikoreksi dan disimpan.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Latihan soal berhasil dikoreksi dan disimpan.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                new OA\Property(
                                    property: 'summary',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total_questions', type: 'integer', example: 5),
                                        new OA\Property(property: 'answered', type: 'integer', example: 5),
                                        new OA\Property(property: 'correct', type: 'integer', example: 4),
                                        new OA\Property(property: 'wrong', type: 'integer', example: 1),
                                        new OA\Property(property: 'score', type: 'number', format: 'float', example: 80.0),
                                        new OA\Property(property: 'is_completed', type: 'boolean', example: true),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'results',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id_question', type: 'integer', example: 1),
                                            new OA\Property(property: 'is_correct', type: 'boolean', example: true),
                                            new OA\Property(property: 'user_answer', type: 'string', example: '104.5°'),
                                            new OA\Property(property: 'correct_answer', type: 'string', example: '104.5°'),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The answers field is required.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Kategori kuis tidak ditemukan.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Kategori kuis tidak ditemukan.'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function store(Request $request, int $id_category): JsonResponse
    {
        $category = Category::find($id_category);

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori kuis tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.id_question' => ['required', 'integer', 'exists:questions,id_question'],
            'answers.*.answer' => ['required'],
        ]);

        $user = $request->user();
        $userId = $user->id_user;

        $questions = Question::query()
            ->where('id_category', $id_category)
            ->with('options')
            ->get()
            ->keyBy('id_question');

        $totalQuestions = $questions->count();
        $correctCount = 0;
        $wrongCount = 0;
        $results = [];

        /** @var list<array{id_question: int, answer: mixed}> $answersList */
        $answersList = $validated['answers'];

        foreach ($answersList as $ansItem) {
            $qId = (int) $ansItem['id_question'];
            $question = $questions->get($qId);

            if (! $question) {
                continue;
            }

            $userRawAnswer = $ansItem['answer'];
            $eval = $this->evaluateAnswer($question, $userRawAnswer);

            $isCorrect = $eval['is_correct'];
            $storedAnswerUser = $eval['user_answer'];
            $correctAnswer = $eval['correct_answer'];

            if ($isCorrect) {
                $correctCount++;
            } else {
                $wrongCount++;
            }

            // Simpan atau perbarui record pengerjaan di tabel exercises
            Exercise::updateOrCreate(
                [
                    'id_user' => $userId,
                    'id_question' => $qId,
                ],
                [
                    'answer_user' => $storedAnswerUser,
                    'is_correct' => $isCorrect,
                ]
            );

            $results[] = [
                'id_question' => $qId,
                'is_correct' => $isCorrect,
                'user_answer' => $storedAnswerUser,
                'correct_answer' => $correctAnswer,
            ];
        }

        $answeredCount = count($results);
        $score = $totalQuestions > 0
            ? round(($correctCount / $totalQuestions) * 100, 1)
            : 0.0;

        // Otomatis memperbarui progres kategori (UserCategoryProgress)
        UserCategoryProgress::updateOrCreate(
            [
                'id_user' => $userId,
                'id_category' => $id_category,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Latihan soal berhasil dikoreksi dan disimpan.',
            'data' => [
                'id_category' => $id_category,
                'summary' => [
                    'total_questions' => $totalQuestions,
                    'answered' => $answeredCount,
                    'correct' => $correctCount,
                    'wrong' => $wrongCount,
                    'score' => $score,
                    'is_completed' => true,
                ],
                'results' => $results,
            ],
        ]);
    }

    /**
     * Get the student's latest exercise result / report for a specific category.
     */
    #[OA\Get(
        path: '/exercises/{id_category}/result',
        summary: 'Rapor Hasil Latihan Kategori',
        description: 'Mengambil riwayat/rapor pengerjaan kuis siswa khusus untuk kategori tersebut, mencakup skor nilai, jumlah benar/salah, tanggal pengerjaan, dan review butir soal.',
        tags: ['Latihan & Soal (Exercise)'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'id_category',
                in: 'path',
                description: 'ID kategori kuis',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Rapor hasil latihan kategori berhasil diambil.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Rapor hasil latihan kategori berhasil diambil.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'category',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id_category', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Geometri Molekul'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'summary',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total_questions', type: 'integer', example: 5),
                                        new OA\Property(property: 'answered', type: 'integer', example: 5),
                                        new OA\Property(property: 'correct', type: 'integer', example: 4),
                                        new OA\Property(property: 'wrong', type: 'integer', example: 1),
                                        new OA\Property(property: 'score', type: 'number', format: 'float', nullable: true, example: 80.0),
                                        new OA\Property(property: 'is_completed', type: 'boolean', example: true),
                                        new OA\Property(property: 'last_attempt_at', type: 'string', format: 'date-time', nullable: true, example: '2026-09-28T22:30:00.000000Z'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'review',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id_question', type: 'integer', example: 1),
                                            new OA\Property(property: 'question_name', type: 'string', example: 'Berapakah sudut ikatan pada molekul air (H2O)?'),
                                            new OA\Property(property: 'question_type', type: 'string', example: 'multiple_choice'),
                                            new OA\Property(property: 'is_answered', type: 'boolean', example: true),
                                            new OA\Property(property: 'is_correct', type: 'boolean', example: true),
                                            new OA\Property(property: 'user_answer', type: 'string', nullable: true, example: '104.5°'),
                                            new OA\Property(property: 'correct_answer', type: 'string', example: '104.5°'),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Kategori kuis tidak ditemukan.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Kategori kuis tidak ditemukan.'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function result(Request $request, int $id_category): JsonResponse
    {
        $category = Category::find($id_category);

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori kuis tidak ditemukan.',
            ], 404);
        }

        $userId = $request->user()->id_user;

        $questions = Question::query()
            ->where('id_category', $id_category)
            ->with('options')
            ->orderBy('id_question')
            ->get();

        $questionIds = $questions->pluck('id_question');

        $userExercises = Exercise::query()
            ->where('id_user', $userId)
            ->whereIn('id_question', $questionIds)
            ->get()
            ->keyBy('id_question');

        $catProgress = UserCategoryProgress::query()
            ->where('id_user', $userId)
            ->where('id_category', $id_category)
            ->first();

        $totalQuestions = $questions->count();
        $answeredCount = $userExercises->count();
        $correctCount = $userExercises->where('is_correct', true)->count();
        $wrongCount = $answeredCount - $correctCount;

        $score = null;
        if ($answeredCount > 0 && $totalQuestions > 0) {
            $score = round(($correctCount / $totalQuestions) * 100, 1);
        }

        $lastAttemptAt = $userExercises->max('created_at') ?? $catProgress?->completed_at;

        $review = $questions->map(function (Question $q) use ($userExercises) {
            $exercise = $userExercises->get($q->id_question);
            $correctAnswer = $this->getCorrectAnswerString($q);

            return [
                'id_question' => $q->id_question,
                'question_name' => $q->name,
                'question_type' => $q->question_type,
                'is_answered' => $exercise !== null,
                'is_correct' => $exercise !== null ? (bool) $exercise->is_correct : false,
                'user_answer' => $exercise?->answer_user,
                'correct_answer' => $correctAnswer,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Rapor hasil latihan kategori berhasil diambil.',
            'data' => [
                'category' => [
                    'id_category' => $category->id_category,
                    'name' => $category->name,
                ],
                'summary' => [
                    'total_questions' => $totalQuestions,
                    'answered' => $answeredCount,
                    'correct' => $correctCount,
                    'wrong' => $wrongCount,
                    'score' => $score,
                    'is_completed' => (bool) ($catProgress?->is_completed),
                    'last_attempt_at' => $lastAttemptAt ? Carbon::parse($lastAttemptAt)->toISOString() : null,
                ],
                'review' => $review,
            ],
        ]);
    }

    /**
     * Evaluate the user's answer against the question's correct options.
     *
     * @return array{is_correct: bool, user_answer: string, correct_answer: string}
     */
    protected function evaluateAnswer(Question $question, mixed $userRawAnswer): array
    {
        $type = $question->question_type;
        $isCorrect = false;
        $userAnswerStr = '';
        $correctAnswerStr = $this->getCorrectAnswerString($question);

        switch ($type) {
            case 'multiple_choice':
                $correctOpt = $question->options->firstWhere('is_correct', true);

                if (is_numeric($userRawAnswer)) {
                    $selectedOpt = $question->options->firstWhere('id_option', (int) $userRawAnswer);
                    $userAnswerStr = $selectedOpt !== null ? $selectedOpt->option_text : (string) $userRawAnswer;
                    $isCorrect = $correctOpt && ((int) $userRawAnswer === (int) $correctOpt->id_option);
                } else {
                    $userAnswerStr = trim((string) $userRawAnswer);
                    $isCorrect = $correctOpt && (strcasecmp($userAnswerStr, trim($correctOpt->option_text)) === 0);
                }
                break;

            case 'true_false':
                $norm = strtolower(trim((string) $userRawAnswer));
                if (in_array($norm, ['benar', 'true', '1'], true)) {
                    $userAnswerStr = 'Benar';
                } elseif (in_array($norm, ['salah', 'false', '0'], true)) {
                    $userAnswerStr = 'Salah';
                } else {
                    $userAnswerStr = trim((string) $userRawAnswer);
                }

                $correctOpt = $question->options->firstWhere('is_correct', true);
                $isCorrect = $correctOpt && (strcasecmp($userAnswerStr, trim($correctOpt->option_text)) === 0);
                break;

            case 'multiple_select':
                /** @var list<string|int> $selectedItems */
                $selectedItems = is_array($userRawAnswer) ? $userRawAnswer : explode(',', (string) $userRawAnswer);
                $selectedItems = array_map(fn ($item) => trim((string) $item), $selectedItems);

                $correctOptions = $question->options->where('is_correct', true);
                $correctIds = $correctOptions->pluck('id_option')->map(fn ($id) => (string) $id)->sort()->values()->all();
                $correctTexts = $correctOptions->pluck('option_text')->map(fn ($t) => strtolower(trim($t)))->sort()->values()->all();

                // Cek pencocokan via ID atau via text
                $userSelectedTexts = [];
                $matchedByIds = true;

                foreach ($selectedItems as $item) {
                    if (is_numeric($item)) {
                        $opt = $question->options->firstWhere('id_option', (int) $item);
                        if ($opt) {
                            $userSelectedTexts[] = $opt->option_text;
                        }
                    } else {
                        $userSelectedTexts[] = $item;
                        $matchedByIds = false;
                    }
                }

                $userAnswerStr = implode(', ', $userSelectedTexts ?: $selectedItems);

                if ($matchedByIds) {
                    $sortedUserIds = $selectedItems;
                    sort($sortedUserIds);
                    $isCorrect = ($sortedUserIds === $correctIds);
                } else {
                    $userNormTexts = array_map(fn ($t) => strtolower(trim($t)), $selectedItems);
                    sort($userNormTexts);
                    $isCorrect = ($userNormTexts === $correctTexts);
                }
                break;

            case 'short_answer':
                $correctOpt = $question->options->firstWhere('is_correct', true);
                $correctText = $correctOpt !== null ? trim((string) $correctOpt->option_text) : '';
                $userAnswerStr = trim((string) $userRawAnswer);

                if (is_numeric($userAnswerStr) && is_numeric($correctText)) {
                    $isCorrect = (float) $userAnswerStr === (float) $correctText;
                } else {
                    $isCorrect = (strcasecmp($userAnswerStr, $correctText) === 0);
                }
                break;

            case 'matching':
                /** @var array<mixed> $pairs */
                $pairs = is_array($userRawAnswer) ? $userRawAnswer : [];
                $allMatched = true;
                $formattedPairs = [];

                if (array_is_list($pairs)) {
                    /** @var list<array{option_text?: string, id_option?: int, match_text?: string}> $pairsList */
                    $pairsList = $pairs;
                    foreach ($pairsList as $p) {
                        $left = trim((string) ($p['option_text'] ?? ''));
                        if (empty($left) && isset($p['id_option'])) {
                            $opt = $question->options->firstWhere('id_option', (int) $p['id_option']);
                            $left = $opt !== null ? $opt->option_text : '';
                        }
                        $right = trim((string) ($p['match_text'] ?? ''));
                        $formattedPairs[] = "{$left} => {$right}";

                        $matchedOpt = $question->options->first(function (QuestionOption $opt) use ($left, $right) {
                            return strcasecmp(trim($opt->option_text), $left) === 0
                                && strcasecmp(trim((string) $opt->match_text), $right) === 0;
                        });

                        if (! $matchedOpt) {
                            $allMatched = false;
                        }
                    }
                } else {
                    /** @var array<string, string> $pairsAssoc */
                    $pairsAssoc = $pairs;
                    foreach ($pairsAssoc as $leftKey => $rightVal) {
                        $left = trim((string) $leftKey);
                        $right = trim((string) $rightVal);
                        $formattedPairs[] = "{$left} => {$right}";

                        $matchedOpt = $question->options->first(function (QuestionOption $opt) use ($left, $right) {
                            return strcasecmp(trim($opt->option_text), $left) === 0
                                && strcasecmp(trim((string) $opt->match_text), $right) === 0;
                        });

                        if (! $matchedOpt) {
                            $allMatched = false;
                        }
                    }
                }

                $userAnswerStr = implode(', ', $formattedPairs);
                $isCorrect = $allMatched && count($formattedPairs) === $question->options->count();
                break;
        }

        return [
            'is_correct' => $isCorrect,
            'user_answer' => $userAnswerStr,
            'correct_answer' => $correctAnswerStr,
        ];
    }

    /**
     * Get a human-readable correct answer string for review and report.
     */
    protected function getCorrectAnswerString(Question $question): string
    {
        switch ($question->question_type) {
            case 'multiple_choice':
            case 'true_false':
            case 'short_answer':
                $correctOpt = $question->options->firstWhere('is_correct', true);

                return $correctOpt !== null ? (string) $correctOpt->option_text : '';

            case 'multiple_select':
                return $question->options
                    ->where('is_correct', true)
                    ->pluck('option_text')
                    ->implode(', ');

            case 'matching':
                return $question->options
                    ->map(fn (QuestionOption $opt) => "{$opt->option_text} => {$opt->match_text}")
                    ->implode(', ');

            default:
                return '';
        }
    }
}
