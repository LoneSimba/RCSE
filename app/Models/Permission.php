<?php

namespace App\Models;

use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $owner_id
 * @property string $owner_type
 * @property string $permission
 * @property bool $allow
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Model $owner
 */
class Permission extends Model
{
    protected $fillable = [
        'owner_id',
        'owner_type',
        'permission',
        'allow',
    ];

    public function owner()
    {
        return $this->belongsTo(
            'App\\Models\\' . Str::studly($this->owner_type),
            'owner_id',
            'id'
        );
    }
}
