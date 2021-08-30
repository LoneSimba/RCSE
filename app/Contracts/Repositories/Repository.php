<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

interface Repository
{
    public function makeModel(array $attr): Model;

    public function getPureObjectById(string $id): ?Model;
}
