<?php
declare(strict_types=1);

namespace RCSE\Core\User;
use Exception;
use RCSE\Core\Database\Database;
use RCSE\Core\Database\SelectQuery;
use RCSE\Core\Database\UpdateQuery;
use RCSE\Core\Secure\APermissionUser;
use RCSE\Core\Utils;

class User extends APermissionUser
{

    public array $data = [];
    public array $prefs = [];
    private array $sessions = [];
    private array $ownPerms = [];
    private UserGroup $group;
    private Database $db;

    public function __construct(string $id, Database $db)
    {
        $this->db = $db;
        $this->getData($id);
    }

    /**
     * Updates user permissions
     *
     * @param array $permissions
     * @return void
     * @throws Exception
     */
    public function addPermission(array $permissions) : void
    {
        if (!$this->hasPermission($permissions))
        {
            /*$query = (new UpdateQuery('users', ['`user_perms`'=>':perms']))->addWhere(['user_id'=>':id']);
            $newPerms = json_decode($this->data['user_perms'], true);
            $newPerms += $permission;
            $query->addData([':id'=>$this->data['user_id'], ':perms'=>json_encode($newPerms)]);
            $this->db->executeCustomQuery($query);
            $this->getData($this->data['user_id']);*/

            $this->ownPerms .= $permissions;
            $this->perms = $this->getPermissions();
        }
    }

    /**
     * Returns permissions array including group permissions
     *
     * @return array
     */
    public function getPermissions() : array
    {
        return array_merge($this->group->perms, $this->ownPerms);
    }

    /**
     * Verifies user's account using given key
     *
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function verifyAccount(string $key) : bool
    {
        if (!$this->data['user_verified'])
        {
            if (md5($key) == $this->data['user_key'])
            {
                $query = (new UpdateQuery('users', ['`user_key`'=>null ,'`user_verified`'=>true]))
                    ->addWhere(['`user_id`'=>$this->data['user_id']]);
                $this->db->executeCustomQuery($query);
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Generates key of given length for various usages and writes it to database
     *
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function generateKey(int $length) : string
    {
        $key = Utils::generateKey($length);
        $query = (new UpdateQuery('users', ['`user_key`'=>':key']))
            ->addWhere(['`user_id`'=>$this->data['user_id']]);
        $query->addData([':key'=>md5($key)]);
        $this->db->executeCustomQuery($query);
        return $key;
    }

    public function generateAuthKey() : string
    {
        $key = Utils::generateKey(6);
        $this->db->addQueryData('ins_auth_key_full', [$key, $this->data['user_id'], Utils::getTimestamp()]);
        $this->db->executeAndGetResult('ins_auth_key_full');
        return $key;
    }

    /**
     * Saves changes in user data to database
     *
     * @throws Exception
     */
    public function saveData()
    {
        $query_data = [];
        foreach ($this->data as $key => $val)
        {
            switch ($key)
            {
                case 'user_id':
                case 'group_id':
                case 'user_bdate':
                case 'user_avatar':
                    $query_data[':{$key}'] = $val;
                    break;
                case 'user_prefs':
                    $query_data[':{$key}'] = json_encode($this->prefs);
                    break;
                case 'user_perms':
                    $query_data[':{$key}'] = json_encode(array_flip($this->ownPerms));
                    break;
            }
        }

        $this->db->addQueryData('upd_user_safe_by_id', $query_data);
        $this->db->executeAndGetResult('upd_user_safe_by_id');
    }

    /**
     * Looks for DB entry by given $login, if it exists, compares given password
     *
     * @param string $pass User password
     * @return bool
     * @throws Exception
     */
    public function checkCredentials(string $pass) : bool
    {
        $query = (new SelectQuery('users', ['`user_passhash`']))->addWhere(['user_id' => $this->data['user_id']]);

        $passhash = $this->db->executeCustomQuery($query)[0]['user_passhash'];
        return $passhash == password_hash($pass, PASSWORD_DEFAULT);
    }

    private function getData(string $id) : void
    {
        $this->db->addQueryData('sel_user_full_by_id', [':user_id'=>$id]);
        $this->data = $this->db->executeAndGetResult('sel_user_safe_by_id')[0];
        $this->group = new UserGroup($this->data['group_id'], $this->db);
        $this->prefs = json_decode($this->data['user_prefs'], true);
        $this->ownPerms = array_flip(json_decode($this->data['user_perms'], true));
        $this->perms = $this->getPermissions();
    }
}