<?php

namespace App\Contracts\Models;

interface Permissionable
{
    public function perms();

    public function isAllowed(string $permission): bool;
}
