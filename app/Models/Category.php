<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_category
 * @property string $name
 * @property string $description
 */
class Category extends Model
{
    public $timestamps = false;

    protected $table = 'categories';

    protected $primaryKey = 'id_category';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return HasMany<Material, $this>
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'id_category', 'id_category');
    }

    /**
     * @return HasMany<Question, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'id_category', 'id_category');
    }

    /**
     * @return HasMany<UserCategoryProgress, $this>
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserCategoryProgress::class, 'id_category', 'id_category');
    }
}
