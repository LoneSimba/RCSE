<?php
declare(strict_types=1);

namespace RCSE\Core\User;
use RCSE\Core\Database\Database;

class User
{

    private $db;
    private $login;
    
    public function __construct(string $login, Database $db)
    {
        $this->db = $db;
        $this->login = $login;
        
    }

    /**
     * Checks wether user has permissions
     *
     * @param string $permission
     * @return boolean
     */
    public function hasPermission(string $permission) : bool
    {
    
    }

    public function isUserCreditansCorrect(string $login, string $pass)
    {

    }

    public function getUserData(string $login) : void
    {
        $this->db->addQueryData('sel_user_by_login', ['login' => $login]);
        $this->userData = $this->db->executeAndGetResult('sel_user_by_login');
        $this->userData['user_prefs'] = json_decode($this->userData['user_prefs']);
        $this->userData['user_perms'] = json_decode($this->userData['user_perms']);
        $this->userData['user_group'] = new UserGroup($this->userData['user_group'], $this->db);
    }
}