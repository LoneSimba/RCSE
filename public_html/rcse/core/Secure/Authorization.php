<?php
declare(strict_types=1);

namespace RCSE\Core\Secure;
use Exception;
use RCSE\Core\Control\Log;
use RCSE\Core\Database\Database;
use RCSE\Core\Database\SelectQuery;
use RCSE\Core\User\User;
use RCSE\Core\Utils;

class Authorization
{

    private Database $db;
    private Log $log;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->log = new Log();
    }

    public function login(array $data) : bool
    {
        if (empty($id = $this->findUser($data['login']))) return false;

        $usr = new User($id['user_id'], $this->db);

        if (!$usr->checkCredentials($data['password'])) return false;

        //$this->mail->sendAuth($usr);

    }

    /**
     * @param array $data
     * @return bool
     * @throws Exception
     * @todo Once mail handler is ready key should be send to user
     */
    public function register(array $data) : bool
    {
        $uid = $this->createNewUser($data);

    }

    public function findUser(string $identifier) : array
    {
        $query = new SelectQuery('users', ['`user_id`']);
        if (preg_match(
                "/[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i",
                $identifier))
        {
            $query->addWhere(['user_email'=>$identifier]);
        }
        else if (preg_match(
            "/[0-9A-Za-z]{8}-[0-9A-Za-z]{4}-4[0-9A-Za-z]{3}-[89ABab][0-9A-Za-z]{3}-[0-9A-Za-z]{12}/i",
            $identifier))
        {
            $query->addWhere(['user_id'=>$identifier]);
        }
        else
        {
            $query->addWhere(['user_login'=>$identifier]);
        }

        return $this->db->executeCustomQuery($query);
    }

    /**
     * Generates and inserts new user record into a database.
     *
     * @param array $data User data
     * @return string If succeeds, returns user ID
     * @throws Exception
     */
    private function createNewUser(array $data) : string
    {
        $query_data = [];
        foreach ($data as $key => $val)
            $query_data[':user_'.$key] = htmlspecialchars($val);

        $this->db->addQueryData('sel_group_id_by_name', [':title'=>'User']);
        $query_data[':group_id'] = $this->db->executeAndGetResult('sel_group_id_by_name')[0]['group_id'];
        $query_data[':user_regdate'] = Utils::getTimestamp();
        $query_data[':user_id'] = Utils::generateUUID();
        $query_data[':user_prefs'] = json_encode([]);
        $query_data[':user_perms'] = json_encode([]);
        $query_data[':user_passhash'] = password_hash($query_data[':user_pass'], PASSWORD_DEFAULT);
        unset($query_data[':user_pass']);

        $this->db->addQueryData('ins_user_full', $query_data);
        $this->db->executeAndGetResult('ins_user_full');

        return $query_data[':user_id'];
    }

    /**
     * @param string $user_id
     * @return string
     * @throws Exception
     */
    private function createNewSession(string $user_id) : string
    {
        $query_data = [];
        $query_data[':user_id'] = $user_id;
        $query_data[':session_id'] = Utils::generateUUID();
        $query_data[':session_ips'] = json_encode([Utils::getClientIP()]);
        $query_data[':session_start'] = Utils::getTimestamp();
        $query_data[':session_browser'] = Utils::getClientBrowser();
        $query_data[':session_os'] = Utils::getClientOS();

        $this->db->addQueryData('ins_session_full', $query_data);
        $this->db->executeAndGetResult('ins_session_full');

        return $query_data[':session_id'];
    }
}