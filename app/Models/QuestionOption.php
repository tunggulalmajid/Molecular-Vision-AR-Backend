<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id_option
 * @property int $id_question
 * @property string $option_text
 * @property string|null $match_text
 * @property bool $is_correct
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class QuestionOption extends Model
{
    use SoftDeletes;

    protected $table = 'question_options';

    protected $primaryKey = 'id_option';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_question',
        'option_text',
        'match_text',
        'is_correct',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_question' => 'integer',
            'is_correct' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'id_question', 'id_question');
    }
}
