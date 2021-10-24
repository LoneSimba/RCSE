<?php

namespace App\Contracts\Repositories;

use App\Models\Permission;
use App\ParameterObjects\Source;
use App\Contracts\Models\Permissionable;

use Illuminate\Database\Eloquent\Collection;

interface PermissionRepository
{
    public function createOrUpdate(Source $source, string $permission, bool $allow = true): Permission;

    public function deleteForSourceBySlug(Source $source, string $slug): bool;

    public function deleteForSource(Source $source): int;

    public function findForPermissionableWithAncestors(Permissionable $model): Collection;
}
