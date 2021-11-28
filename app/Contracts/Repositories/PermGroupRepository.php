<?php

namespace App\Contracts\Repositories;

use App\Models\PermGroup;

interface PermGroupRepository
{
    public function create(string $slug, ?string $parentId = null): PermGroup;

    public function update(string $id, array $fields): bool;

    public function updateByParentId(string $parentId, array $fields): int;

    /**
     * Before removal make sure there's no users or groups attached to removed one
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;
}
