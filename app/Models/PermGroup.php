<?php

namespace App\Models;

use App\Traits\Models\HasUuid;
use App\ParameterObjects\Source;
use App\Contracts\Models\{Parameterizable, Permissionable};

use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Eloquent\{Collection, Model};
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
 * @property string $id
 * @property string|null $parent_id
 * @property string $slug
 * @property Carbon|null $created_at
 *
 * @property Collection $users
 * @property PermGroup|null $parent
 * @property Collection $children
 * @property Collection $perms
 */
class PermGroup extends Model implements Parameterizable, Permissionable
{
    use HasUuid;
    use HasRecursiveRelationships;

    const UPDATED_AT = null;

    protected $fillable = [
        'parent_id',
        'slug'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function perms()
    {
        return $this->hasMany(Permission::class, 'owner_id', 'id');
    }

    public function parameterize(): Source
    {
        return new Source($this->id, self::sourceType());
    }

    public static function sourceType(): string
    {
        return Str::snake(class_basename(self::class));
    }

    public function isAllowed(string $permission): bool
    {
        $perm = $this->perms->where('permission', $permission)->first();
        if ($perm) {
            return $perm->allow;
        }

        return (bool)$this->parent?->isAllowed($permission);
    }
}
