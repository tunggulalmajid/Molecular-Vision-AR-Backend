<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id_user_category_progress
 * @property int $id_user
 * @property int $id_category
 * @property bool $is_completed
 * @property Carbon|null $completed_at
 */
class UserCategoryProgress extends Model
{
    public $timestamps = false;

    protected $table = 'user_category_progress';

    protected $primaryKey = 'id_user_category_progress';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'id_category',
        'is_completed',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_user' => 'integer',
            'id_category' => 'integer',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }
}
