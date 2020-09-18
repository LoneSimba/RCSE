<?php
declare(strict_types=1);

namespace RCSE\Core\Control;
use RCSE\Core\User\UserGroup;
use RCSE\Core\User\User;

class Permissions
{
    private $systemPermissionsList = [];
    private $permissionsList = [];
    private $control;

    public function __construct(Control $control)
    {
        $this->control = $control;
    }

    public function addPermission(string $permission) : void
    {

    }
}