<?php

namespace App\Models;

use App\Traits\Models\HasUuid;
use App\ParameterObjects\Source;
use App\Contracts\Models\{Parameterizable, Permissionable};

use Illuminate\Support\{Carbon, Str};
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\{SoftDeletes, Factories\HasFactory};

use Laravel\{Fortify\TwoFactorAuthenticatable, Jetstream\HasProfilePhoto, Jetstream\HasTeams, Sanctum\HasApiTokens};

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $perm_group_id
 * @property string|null $current_team_id
 * @property string|null $profile_photo_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Authenticatable implements Parameterizable, Permissionable
{
    use HasUuid;
    use HasTeams;
    use HasFactory;
    use HasApiTokens;
    use HasProfilePhoto;

    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'social'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'social' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function permGroup()
    {
        return $this->belongsTo(PermGroup::class);
    }

    public function perms()
    {
        return $this->hasMany(Permission::class, 'owner_id', 'id');
    }

    public function parameterize(): Source
    {
        return new Source($this->id, self::sourceType());
    }

    public function isAllowed(string $permission): bool
    {
        $perm = $this->perms->where('permission', $permission)->first();
        if ($perm) {
            return $perm->allow;
        }

        return $this->permGroup->isAllowed($permission);
    }

    public static function sourceType(): string
    {
        return Str::snake(self::class);
    }
}
