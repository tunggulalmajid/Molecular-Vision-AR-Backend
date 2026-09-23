<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id_user_molecule_progress
 * @property int $id_user
 * @property int $id_molecule
 * @property bool $is_completed
 * @property Carbon|null $completed_at
 */
class UserMoleculeProgress extends Model
{
    public $timestamps = false;

    protected $table = 'user_molecule_progress';

    protected $primaryKey = 'id_user_molecule_progress';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'id_molecule',
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
            'id_molecule' => 'integer',
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
     * @return BelongsTo<Molecule, $this>
     */
    public function molecule(): BelongsTo
    {
        return $this->belongsTo(Molecule::class, 'id_molecule', 'id_molecule');
    }
}
