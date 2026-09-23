<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id_material
 * @property string $name
 * @property int $id_category
 * @property string $description
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Material extends Model
{
    use SoftDeletes;

    protected $table = 'materials';

    protected $primaryKey = 'id_material';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'id_category',
        'description',
        'content',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_category' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    /**
     * @return HasMany<UserMaterialProgress, $this>
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserMaterialProgress::class, 'id_material', 'id_material');
    }
}
