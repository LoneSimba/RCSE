<?php

namespace App\Repositories;

use App\ParameterObjects\Source;
use App\Contracts\Models\Permissionable;
use App\Models\{PermGroup, Permission, User};
use App\Contracts\Repositories\PermissionRepository as PermissionRepositoryContract;

use Illuminate\Database\Eloquent\Collection;

class PermissionRepository extends Repository implements PermissionRepositoryContract
{

    public function createOrUpdate(Source $source, string $permission, bool $allow = true): Permission
    {
        return $this->getModel()->updateOrCreate(
            [
                'owner_id' => $source->id,
                'owner_type'=> $source->type,
                'slug' => $permission,
            ],
            ['allow' => $allow]
        );
    }

    public function deleteForSourceBySlug(Source $source, string $slug): bool
    {
        return (bool)$this->getModel()
            ->where([
                'owner_id'=> $source->id,
                'owner_type' => $source->type,
                'slug' => $slug,
            ])
            ->delete();
    }

    public function deleteForSource(Source $source): int
    {
        return $this->getModel()
            ->where([
                'owner_id' => $source->id,
                'owner_type' => $source->type,
            ])
            ->delete();
    }

    public function findForPermissionableWithAncestors(Permissionable $model): Collection
    {
        if ($model instanceof PermGroup) {
            $ids = $model->ancestorsAndSelf()
                ->get('id')
                ->pluck('id')
                ->all();
        } elseif ($model instanceof User) {
            $ids = $model->permGroup()
                ->with('ancestorsAndSelf')
                ->first()
                ->ancestorsAndSelf()
                ->pluck('id')
                ->all();
            array_push($ids, $model->id);
        }

        return $this->getModel()
            ->whereIn('owner_id', $ids)
            ->get();
    }
}
