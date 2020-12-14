<?php
declare(strict_types=1);

namespace RCSE\Core\User;
use Exception;
use RCSE\Core\Database\Database;
use RCSE\Core\Database\UpdateQuery;
use RCSE\Core\Secure\APermissionUser;

class UserGroup extends APermissionUser
{
    public array $data = [];
    private Database $db;
    private UserGroup $parentGroup;
    
    public function __construct(string $id, Database $db)
    {
        $this->db = $db;
        $this->getData($id);
    }

    public function updateData()
    {
        $this->db->addQueryData('upd_group_by_id',$this->data);
        $this->db->executeAndGetResult('upd_group_by_id');
    }

    /**
     * Updates DB entry to add $permission
     *
     * @param array $permissions Permission array to add
     * @return void
     * @throws Exception
     */
    public function addPermission(array $permissions) : void
    {
        if (!$this->hasPermission($permissions))
        {
            $query = (new UpdateQuery('groups', ['`group_perms`'=>':perms']))->addWhere(['group_id'=>':id']);
            $newPerms = json_decode($this->data['group_perms'], true);
            $newPerms += $permissions;
            $query->addData([':id'=>$this->data['group_id'], ':perms'=>json_encode($newPerms)]);
            $this->db->executeCustomQuery($query);
            $this->getData($this->data['group_id']);
        }
        
    }

    /**
     * Gets permissions array including all parent groups
     *
     * @return array
     */
    public function getPermissions() : array
    {
        $parent = [];
        if (isset($this->parentGroup))
            $parent = json_decode($this->parentGroup->data['group_perms'], true);
        return array_flip(array_merge($parent, json_decode($this->data['group_perms'], true)));
    }

    private function getData(string $id) : void
    {
        $this->db->addQueryData('sel_group_by_id', [':id' => $id]);
        $this->data = $this->db->executeAndGetResult('sel_group_by_id')[0];
        if (isset($this->data['group_parent_id']))
            $this->parentGroup = new UserGroup($this->data['group_parent_id'], $this->db);
        $this->perms = $this->getPermissions();
    }

}