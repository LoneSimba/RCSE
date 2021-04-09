<?php
declare(strict_types=1);

namespace RCSE\Core\User;
use Exception;
use RCSE\Core\Database\Database;
use RCSE\Core\Database\InsertQuery;
use RCSE\Core\Database\SelectQuery;
use RCSE\Core\Database\UpdateQuery;
use RCSE\Core\Secure\APermissionUser;
use RCSE\Core\Statics\Utils;

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
    public function addPermission(array $permissions): void
    {
        if (!$this->hasPermission($permissions)) {
            /*$query = (new UpdateQuery('users', ['`user_perms`'=>':perms']))->addWhere(['user_id'=>':id']);
            $newPerms = json_decode($this->data['user_perms'], true);
            $newPerms += $permission;
            $query->addData([':id'=>$this->data['user_id'], ':perms'=>json_encode($newPerms)]);
            $this->db->executeCustomQuery($query);
            $this->getData($this->data['user_id']);*/

            $this->ownPerms[] = $permissions;
            $this->perms = $this->getPermissions();
        }
    }

    /**
     * Returns permissions array including group permissions
     *
     * @return array
     */
    public function getPermissions(): array
    {
        return array_merge($this->group->perms, $this->ownPerms);
    }

    public function changePassword(string $password): void
    {
        $this->db->addQueryData('upd_user_credentials_by_id', [
            ':user_id' => $this->data['user_id'],
            ':user_login' => $this->data['user_login'],
            ':user_email' => $this->data['user_email'],
            ':user_passhash' => password_hash($password, PASSWORD_DEFAULT)
        ]);
        $this->db->executeAndGetResult('upd_user_credentials_by_id');
    }

    /**
     * Verifies user's account using given key
     *
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function verifyAccount(string $key): bool
    {
        if (!(bool) $this->data['user_verified']) {
            if (!$this->verifyKey($key)) {
                return false;
            }

            $query = (new UpdateQuery('users', [ '`user_verified`' => true ]))
                ->addWhere([ '`user_id`' => $this->data['user_id'] ]);
            $this->db->executeCustomQuery($query);
        }

        return true;
    }

    /**
     * Generates key of given length for various usages and writes it to database
     *
     * @return string
     * @throws Exception
     */
    public function generateVerificationKey(): string
    {
        $key = Utils::generateKey(6);
        $query = (new UpdateQuery('users', [ '`user_key`' ]))
            ->addWhere([ '`user_id`' => $this->data['user_id'] ]);
        $query->addData([ ':user_key' => md5($key) ]);
        $this->db->executeCustomQuery($query);
        return $key;
    }

    /**
     * Verifies given key
     *
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function verifyKey(string $key): bool
    {
        if (md5($key) == $this->data['user_key']) {
            $query = (new UpdateQuery('users', [ '`user_key`' ]))
                ->addWhere([ '`user_id`' => $this->data['user_id' ]]);
            $query->addData([ ':user_key' => null ]);
            $this->db->executeCustomQuery($query);
            return true;
        } else {
            return false;
        }

    }

    public function generateAuthKey(): string
    {

        $key = Utils::generateKey(3);
        $this->db->addQueryData('ins_auth_key_full', [
            ':key_id' => $key,
            ':user_id' => $this->data['user_id'],
            ':key_expires' => Utils::getTimestamp('Y-m-d H:i:s', "+10 minutes")
        ]);
        $this->db->executeAndGetResult('ins_auth_key_full');
        return $key;
    }

    public function verifyAuthKey(string $key): bool
    {
        $this->db->addQueryData('sel_auth_key_by_user_id', [
            ':key_id' => $key,
            ':user_id' => $this->data['user_id']
        ]);

        if(!empty($this->db->executeAndGetResult('sel_auth_key_by_user_id')[0])) {
            $this->db->addQueryData('del_auth_key', [ ':key_id' => $key ]);
            $this->db->executeAndGetResult('del_auth_key');
            return true;
        } else {
            return false;
        }
    }

    public function getSecretQuestion(): array
    {
        $query = (new SelectQuery('secret_questions', [ '`*`' ]))
            ->addWhere([ '`user_id`' => $this->data['user_id'] ]);
        return $this->db->executeCustomQuery($query)[0];
    }

    public function setSecretQuestion(string $text, string $answer): void
    {
        $query = (new InsertQuery('secret_questions', [ '`question_text`', '`question_answer`' ]))->addUpdate();
        $query->addData([ ':question_text' => $text, ':question_answer' => md5($answer) ]);
        $this->db->executeCustomQuery($query);
    }

    public function checkSecretQuestion(string $answer): bool
    {
        return !empty($question = $this->getSecretQuestion()) && ($question['question_answer'] == md5($answer));
    }

    /**
     * Saves changes in user data to database
     *
     * @throws Exception
     */
    public function saveData(): void
    {
        $query_data = [];
        foreach ($this->data as $key => $val) {
            switch ($key) {
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
    public function checkCredentials(string $pass): bool
    {
        $this->db->addQueryData('sel_user_passhash_by_id', [ ':user_id' => $this->data['user_id'] ]);
        $passhash = $this->db->executeAndGetResult('sel_user_passhash_by_id')[0]['user_passhash'];
        return password_verify($pass, $passhash);
    }

    private function getData(string $id): void
    {
        $this->db->addQueryData('sel_user_safe_by_id', [ ':user_id' => $id ]);
        $this->data = $this->db->executeAndGetResult('sel_user_safe_by_id')[0];
        $this->group = new UserGroup($this->data['group_id'], $this->db);
        $this->prefs = json_decode($this->data['user_prefs'], true);
        $this->ownPerms = array_flip(json_decode($this->data['user_perms'], true));
        $this->perms = $this->getPermissions();
    }
}