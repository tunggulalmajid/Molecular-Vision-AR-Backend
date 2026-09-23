<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id_degree
 * @property string $name
 * @property Carbon $created_at
 */
class Degree extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'degrees';

    protected $primaryKey = 'id_degree';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_degree', 'id_degree');
    }
}
