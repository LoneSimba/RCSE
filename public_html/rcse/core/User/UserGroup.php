<?php
declare(strict_types=1);

namespace RCSE\Core\User;
use RCSE\Core\Database\Database;

class UserGroup
{
    public $groupData = [];
    private $db;
    
    public function __construct(string $groupId, Database $db)
    {
        $this->db = $db;
        $this->getGroupData($groupId);
    }

    public function updateGroupData()
    {
        $this->db->addQueryData('upd_group_by_id',$this->userData);
        $this->db->executeAndGetResult('upd_group_by_id');
    }

    /**
     * Checks wether user has permissions
     *
     * @param string $permission
     * @return boolean
     */
    public function hasPermission(string $permission) : bool
    {
        return (bool) array_search($permission, $this->groupData['group_perms']);
    }

    private function getGroupData(string $groupId) : void
    {
        $this->db->addQueryData('sel_group_by_id', ['id' => $groupId]);
        $this->userData = $this->db->executeAndGetResult('sel_group_by_id');
        $this->userData['user_perms'] = json_decode($this->groupData['user_perms']);
    }
}