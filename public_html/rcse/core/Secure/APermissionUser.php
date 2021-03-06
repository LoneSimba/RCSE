<?php
declare(strict_types=1);

namespace RCSE\Core\Secure;

abstract class APermissionUser
{
    /**
     * @var array Contains decoded permissions of object. Note - in DB array is flipped (keys is values).
     */
    public array $perms = [];
    
    /**
     * Checks whether has permissions.
     *
     * @param array $permissions Array of permissions to check.
     * @return bool
     */
    public function hasPermission(array $permissions) : bool
    {
        $err_cnt = 0;
        foreach ($permissions as $key => $val)
        {
            if (!isset($this->perms[$val])) $err_cnt++;
        }

        return ($err_cnt > 0);
    }
    
    abstract public function getPermissions() : array;
    abstract public function addPermission(array $permissions) : void;
}