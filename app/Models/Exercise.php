<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id_exercise
 * @property int $id_question
 * @property int $id_user
 * @property string $answer_user
 * @property bool $is_correct
 * @property Carbon $created_at
 */
class Exercise extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'exercises';

    protected $primaryKey = 'id_exercise';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_question',
        'id_user',
        'answer_user',
        'is_correct',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_question' => 'integer',
            'id_user' => 'integer',
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

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
