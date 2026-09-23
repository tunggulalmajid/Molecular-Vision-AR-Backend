<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id_molecule
 * @property string $name
 * @property string $model_3d_url
 * @property string $formula
 * @property string $shape
 * @property string $bent
 * @property string $bond_type
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Molecule extends Model
{
    use SoftDeletes;

    protected $table = 'molecules';

    protected $primaryKey = 'id_molecule';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'model_3d_url',
        'formula',
        'shape',
        'bent',
        'bond_type',
        'description',
    ];

    /**
     * @return HasMany<UserMoleculeProgress, $this>
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserMoleculeProgress::class, 'id_molecule', 'id_molecule');
    }
}
