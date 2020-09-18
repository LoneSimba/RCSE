<?php
declare(strict_types=1);

namespace RCSE\Core\User;
use RCSE\Core\Database\Database;

class User
{

    public $userData = [];
    private $db;
    
    public function __construct(string $login, Database $db)
    {
        $this->db = $db;
        $this->getUserData($login);
    }

    public function updateUserData()
    {
        $this->db->addQueryData('upd_user_by_login',$this->userData);
        $this->db->executeAndGetResult('upd_user_by_login');
    }

    /**
     * Checks wether user has permissions
     *
     * @param string $permission
     * @return boolean
     */
    public function hasPermission(string $permission) : bool
    {
        return (bool) array_search($permission, $this->userData['user_perms']);
    }

    private function getUserData(string $login) : void
    {
        $this->db->addQueryData('sel_user_by_login', ['login' => $login]);
        $this->userData = $this->db->executeAndGetResult('sel_user_by_login');
        $this->userData['user_prefs'] = json_decode($this->userData['user_prefs']);
        $this->userData['user_perms'] = json_decode($this->userData['user_perms']);
    }
}