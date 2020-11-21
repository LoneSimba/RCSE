<?php
declare(strict_types=1);

namespace RCSE\Core\Secure;
use Exception;
use RCSE\Core\Database\Database;
use RCSE\Core\Database\SelectQuery;
use RCSE\Core\Utils;

class Authorization
{

    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function login(array $data)
    {
        
    }

    public function register(array $data)
    {
        $this->insertNewUser($data);
    }

    /**
     * Looks for DB entry by given $login, if it exists, compares given password
     *
     * @param string $login One of user identifiers - UUID, login or email
     * @param string $pass User password
     * @return boolean
     * @throws Exception
     */
    public function checkCredentials(string $login, string $pass) : bool
    {
        $query = (new SelectQuery('users', ['`user_passhash`']));
        if (preg_match("/[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i", $login))
        {
            $query->addWhere(['`user_email`'=>"'".$login."'"]);
        }
        else if (preg_match("/[0-9A-Za-z]{8}-[0-9A-Za-z]{4}-4[0-9A-Za-z]{3}-[89ABab][0-9A-Za-z]{3}-[0-9A-Za-z]{12}/i", $login))
        {
            $query->addWhere(['`user_id`'=>"'".$login."'"]);
        }
        else 
        {
            $query->addWhere(['`user_login`'=>"'".$login."'"]);
        }

        $passhash = $this->db->executeCustomQuery($query)[0]['user_passhash'];
        return $passhash == password_hash($pass, PASSWORD_DEFAULT);

    }

    private function insertNewUser(array $data) : bool
    {
        $query_data = [];
        foreach ($data as $key => $val)
        {
            $query_data[':user_'.$key] = htmlspecialchars($val);
        }

        $query_data[':user_passhash'] = password_hash($query_data[':user_pass'], PASSWORD_DEFAULT);
        unset($query_data[':user_pass']);
        $this->db->addQueryData('sel_group_id_by_name', [':title'=>'User']);
        $query_data[':group_id'] = $this->db->executeAndGetResult('sel_group_id_by_name')[0]['group_id'];
        $query_data[':user_regdate'] = Utils::getTimestamp();
        $query_data[':user_id'] = Utils::generateUUID();
        $query_data[':user_prefs'] = json_encode([]);
        $query_data[':user_perms'] = json_encode([]);

        $this->db->addQueryData('ins_user_full', $query_data);
        $res = $this->db->executeAndGetResult('ins_user_full');

        return ($res) ? true : $res;
    }
}