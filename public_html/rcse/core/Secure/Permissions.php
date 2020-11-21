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

    public function addCustomPermission(string $permission, string $name) : void
    {
        if (!array_key_exists($permission, $this->permissionsList)) $this->permissionsList[$permission] = $name;
    }

    public function addPermissionToTarget(APermissionUser $target, array $permission)
    {
        $target->addPermission($permission);
    }

    public function targetHasPermission(APermissionUser $target, array $permission) : bool
    {
         return ($this->permissionExists($permission)) ? $target->hasPermission($permission) : false;
    }

    public function getTargetPermission(APermissionUser $target) : array
    {
        return $target->getPermissions();
    }

    public function getPermissionsList() : array
    {
        return [$this->systemPermissionsList, $this->permissionsList];
    }

    public function permissionExists(array $permission) : bool
    {
        return array_key_exists($permission, $this->getPermissionsList());
    }

    private function fillPermissionsList()
    {

    }
}