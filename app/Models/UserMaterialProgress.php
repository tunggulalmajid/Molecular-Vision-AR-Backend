<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id_user_material_progress
 * @property int $id_user
 * @property int $id_material
 * @property bool $is_completed
 * @property Carbon|null $completed_at
 */
class UserMaterialProgress extends Model
{
    public $timestamps = false;

    protected $table = 'user_material_progress';

    protected $primaryKey = 'id_user_material_progress';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'id_material',
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
            'id_material' => 'integer',
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
     * @return BelongsTo<Material, $this>
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }
}
