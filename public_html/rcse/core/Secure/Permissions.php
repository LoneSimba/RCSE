<?php
declare(strict_types=1);

namespace RCSE\Core\Secure;

class Permissions
{
    private array $systemPermissionsList = [];
    private array $permissionsList = [];

    public function __construct()
    {
        $this->fillPermissionsList();
    }

    /**
     * Adds new permission to permissions list
     *
     * @param string $permission
     * @param string $name
     */
    public function addCustomPermission(string $permission, string $name) : void
    {
        if (!array_key_exists($permission, $this->permissionsList)) $this->permissionsList[$permission] = $name;
    }

    /**
     * Adds new $permissions to specified $target
     *
     * @param APermissionUser $target
     * @param array $permissions
     */
    public function addPermissionToTarget(APermissionUser $target, array $permissions)
    {
        $target->addPermission($permissions);
    }

    /**
     * Checks whether specified $target has $permissions. If any permission from array is not available in system, returns false
     *
     * @param APermissionUser $target
     * @param array $permissions
     * @return bool
     */
    public function targetHasPermission(APermissionUser $target, array $permissions) : bool
    {
         return ($this->permissionExists($permissions)) ? $target->hasPermission($permissions) : false;
    }

    /**
     * Obtains $target's permissions
     *
     * @param APermissionUser $target
     * @return array
     */
    public function getTargetPermissions(APermissionUser $target) : array
    {
        return $target->getPermissions();
    }

    /**
     * Gets permissions available in system
     *
     * @return array
     */
    public function getPermissionsList() : array
    {
        return [$this->systemPermissionsList, $this->permissionsList];
    }

    /**
     * Checks whether specified permissions exist in system
     *
     * @param array $permissions
     * @return bool
     */
    public function permissionExists(array $permissions) : bool
    {
        return array_key_exists($permissions, $this->getPermissionsList());
    }

    /**
     * Fills $systemPermissionsList with default permissions (and you - with determination)
     *
     * @returns void
     */
    private function fillPermissionsList() : void
    {

    }
}