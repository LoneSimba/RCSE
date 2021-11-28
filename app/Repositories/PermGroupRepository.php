<?php

namespace App\Repositories;

use App\Models\PermGroup;
use App\Contracts\Repositories\PermGroupRepository as RepositoryContract;

class PermGroupRepository extends Repository implements RepositoryContract
{
    public function create(string $slug, ?string $parentId = null): PermGroup
    {
        return $this->getModel()->create([
            'slug' => $slug,
            'parent_id' => $parentId,
        ]);
    }

    public function update(string $id, array $fields): bool
    {
        return $this->getModel()
            ->where('id', $id)
            ->update($fields);
    }

    public function updateByParentId(string $parentId, array $fields): int
    {
        return $this->getModel()
            ->where('parent_id', $parentId)
            ->update($fields);
    }

    public function delete(string $id): bool
    {
        return $this->getModel()->destroy($id);
    }
}
