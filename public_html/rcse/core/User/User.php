<?php
declare(strict_types=1);

namespace RCSE\Core\User;
use Exception;
use RCSE\Core\Database\Database;
use RCSE\Core\Database\UpdateQuery;
use RCSE\Core\Secure\APermissionUser;

class User extends APermissionUser
{

    public array $data = [];
    public array $prefs = [];
    private array $sessions = [];
    private UserGroup $group;
    private Database $db;

    public function __construct(string $id, Database $db)
    {
        $this->db = $db;
        $this->getData($id);
        
    }

    /**
     * Updates DB entry to add $permission
     *
     * @param array $permission
     * @return void
     * @throws Exception
     */
    public function addPermission(array $permission)
    {
        if (!$this->hasPermission($permission))
        {
            $query = (new UpdateQuery('users', ['`user_perms`'=>':perms']))->addWhere(['user_id'=>':id']);
            $newPerms = json_decode($this->data['user_perms'], true);
            $newPerms += $permission;
            $query->addData([':id'=>$this->data['user_id'], ':perms'=>json_encode($newPerms)]);
            $this->db->executeCustomQuery($query);
            $this->getData($this->data['user_id']);
        }
    }

    /**
     * Returns permissions array including group permissions
     *
     * @return array
     */
    public function getPermissions() : array
    {
        return array_merge($this->group->perms, array_flip(json_decode($this->data['user_perms'], true)));
    }

    private function getData(string $id)
    {
        $this->db->addQueryData('sel_user_full_by_id', [':id'=>$id]);
        $this->data = $this->db->executeAndGetResult('sel_user_full_by_id')[0];
        $this->group = new UserGroup($this->data['group_id'], $this->db);
        $this->prefs = json_decode($this->data['user_prefs'], true);
        $this->perms = $this->getPermissions();
    }
}