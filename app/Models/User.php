<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id_user
 * @property int|null $id_degree
 * @property int|null $id_role
 * @property string $name
 * @property string|null $school
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_degree',
        'id_role',
        'name',
        'school',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_degree' => 'integer',
            'id_role' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Backward-compatibility accessor for $user->id.
     */
    public function getIdAttribute(): ?int
    {
        return isset($this->attributes['id_user']) ? (int) $this->attributes['id_user'] : null;
    }

    /**
     * @return BelongsTo<Degree, $this>
     */
    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class, 'id_degree', 'id_degree');
    }

    /**
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id');
    }

    /**
     * @return HasMany<Exercise, $this>
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'id_user', 'id_user');
    }

    /**
     * @return HasMany<UserMoleculeProgress, $this>
     */
    public function moleculeProgress(): HasMany
    {
        return $this->hasMany(UserMoleculeProgress::class, 'id_user', 'id_user');
    }

    /**
     * @return HasMany<UserMaterialProgress, $this>
     */
    public function materialProgress(): HasMany
    {
        return $this->hasMany(UserMaterialProgress::class, 'id_user', 'id_user');
    }

    /**
     * @return HasMany<UserCategoryProgress, $this>
     */
    public function categoryProgress(): HasMany
    {
        return $this->hasMany(UserCategoryProgress::class, 'id_user', 'id_user');
    }
}
