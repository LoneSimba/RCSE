<?php
declare(strict_types=1);

namespace RCSE\Core\Secure;

use Exception;
use RCSE\Core\Control\Config;
use RCSE\Core\Control\Log;
use RCSE\Core\Control\Mail;
use RCSE\Core\Database\Database;
use RCSE\Core\Database\SelectQuery;
use RCSE\Core\Statics\Utils;
use RCSE\Core\User\User;

class Authorization
{
    private Database $db;
    private Log $log;
    private array $mailAddresses;
    private bool $isMailUsed;

    public function __construct(Database $db, Log $log)
    {
        $this->db = $db;
        $this->log = $log;
        $this->mailAddresses = (new Config())->getConfig("mailer")["addresses"];
        $this->isMailUsed = (new Config())->getConfig("site")["mail-features"];
    }

    /**
     * Signs in user using password. If email feature is enabled, generates 2f key instead
     *
     * @param array $data
     * @return string userLoggedIn if sign in is successful, 2FRequired if 2f is required,
     *             userNotFound if user not found, userNotVerified if user account is not verified, userWrongPassword if password is incorrect
     * @throws Exception
     */
    public function login1F(array $data) : string
    {
        if (empty($id = $this->findUserId($data['login']))) {
            return 'userNotFound';
        }

        $usr = new User($id, $this->db);

        if ((bool) $usr->data['user_verified']) {
            if ($usr->checkCredentials($data['password'])) {
                if ($this->isMailUsed) {
                    $this->sendNewAuthKey($id);
                    return '2FRequired';
                } else {
                    $this->createNewSession($id);
                    return 'userLoggedIn';
                }
            } else {
                return 'userWrongPassword';
            }
        } else {
            return 'userNotVerified';
        }
    }

    /**
     * Signs in user using second factor
     *
     * @param array $data
     * @return string userLoggedIn if sign in successful, userWrongKey if key is incorrect
     * @throws Exception
     */
    public function login2F(array $data) : string
    {
        $usr = new User($this->findUserId($data['login']), $this->db);

        if ($usr->verifyAuthKey($data['key'])) {
            $this->createNewSession($usr->data['user_id']);
            return 'userLoggedIn';
        } else {
            return 'userWrongKey';
        }
    }

    /**
     * @param array $data
     * @return string userRegistered if successful registration with 2F, userRegisteredNo2F w/o 2F (should manually return
     *                  restoration key), userRegistrationFailed if not successful
     * @throws Exception
     */
    public function register(array $data) : string
    {
        try {
            $uid = $this->createNewUser($data);
        } catch (Exception $e) {
            return 'userRegistrationFailed';
        }

        if ($this->isMailUsed) {
            $this->sendVerificationKey($uid);
            return 'userRegistered';
        } else {
            return 'userRegisteredNo2F';
        }
    }

    public function getUserSecretQuestion(string $user_id) : string
    {
        if (empty($id = $this->findUserId($user_id))) {
            return 'userNotFound';
        }

        $usr = new User($id, $this->db);

        return $usr->getSecretQuestion()['question_text'];
    }

    /**
     * Sends new auth key
     *
     * @param string $user_id
     * @return string authKeySent if email sent successfully, emailNotSent if not
     */
    public function sendNewAuthKey(string $user_id) : string
    {
        $usr = new User($user_id, $this->db);
        $mail = new Mail($this->log, [ $this->mailAddresses['no-reply'], 'No Reply' ], [ [$usr->data['user_email'], $usr->data['user_login'] ] ]);

        $mail->setMessage("Auth code", "<b>".$usr->generateAuthKey()."</b>");
        return $mail->send() ? 'authKeySent' : 'emailSendingFailed';
    }

    /**
     * @param string $login
     * @return string success if password restoration key sent, emailDisabled if mail is not set up,
     *                  userNotFound is user not found, emailNotSent if email send failed
     */
    public function requireRestorePassword(string $login) : string
    {
        if (empty($id = $this->findUserId($login))) {
            return 'userNotFound';
        }

        if ($this->isMailUsed) {
            try {
                $this->sendPassRestoreKey($id);
                return 'restoreKeySent';
            } catch (Exception $e) {
                return 'emailSendingFailed';
            }
        } else {
            return 'emailDisabled';
        }
    }

    public function restorePasswordWithKey(array $data) : string
    {
        if (empty($id = $this->findUserId($data['login']))) {
            return 'userNotFound';
        }

        $usr = new User($id, $this->db);

        if ($usr->verifyKey($data['key'])) {
            return $this->changePassword($data);
        } else {
            return 'restoreWrongKey';
        }
    }

    public function restorePasswordWithQuestion(array $data) : string
    {
        if (empty($id = $this->findUserId($data['login']))) {
            return 'userNotFound';
        }

        $usr = new User($id, $this->db);

        if ($usr->checkSecretQuestion($data['answer'])) {
            return $this->changePassword($data);
        } else {
            return 'restoreWrongAnswer';
        }
    }

    public function changePassword(array $data) : string
    {
        if (empty($id = $this->findUserId($data['login']))) {
            return 'userNotFound';
        }

        $usr = new User($id, $this->db);

        try {
            $usr->changePassword($data['password']);
            return 'passwordChanged';
        } catch(Exception $e) {
            return 'passwordChangeFailed';
        }
    }

    public function verifyAccount(array $data) : string
    {
        $usr = new User($this->findUserId($data['uid']), $this->db);

        if ($usr->verifyAccount($data['key'])) {
            return 'success';
        } else {
            return 'userVerificationFailed';
        }
    }


    public function findUserId(string $identifier) : string
    {
        $query = new SelectQuery('users', ['`user_id`']);
        if (preg_match(
                "/[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i",
                $identifier)) {
            $query->addWhere(['`user_email`'=>$identifier]);
        } elseif (preg_match(
            "/[0-9A-Za-z]{8}-[0-9A-Za-z]{4}-4[0-9A-Za-z]{3}-[89ABab][0-9A-Za-z]{3}-[0-9A-Za-z]{12}/i",
            $identifier)) {
            $query->addWhere(['`user_id`'=>$identifier]);
        } else {
            $query->addWhere(['`user_login`'=>$identifier]);
        }

        return $this->db->executeCustomQuery($query)[0]['user_id'] ?? "";
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
        foreach ($data as $key => $val) {
            $query_data[':user_' . $key] = htmlspecialchars($val);
        }

        $this->db->addQueryData('sel_group_id_by_name', [':title'=>'User']);
        $query_data[':group_id'] = $this->db->executeAndGetResult('sel_group_id_by_name')[0]['group_id'];
        $query_data[':user_regdate'] = Utils::getTimestamp();
        $query_data[':user_id'] = Utils::generateUUID();
        $query_data[':user_prefs'] = json_encode([]);
        $query_data[':user_perms'] = json_encode([]);
        $query_data[':user_passhash'] = password_hash($query_data[':user_pass'], PASSWORD_DEFAULT);
        $query_data[':user_verified'] = ($this->isMailUsed) ? 0 : 1;
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
        $query_data[':user_id'] = $this->findUserId($user_id);
        $query_data[':session_id'] = Utils::generateUUID();
        $query_data[':session_ips'] = json_encode([Utils::getClientIP()]);
        $query_data[':session_start'] = Utils::getTimestamp();
        $query_data[':session_browser'] = Utils::getClientBrowser();
        $query_data[':session_os'] = Utils::getClientOS();

        $this->db->addQueryData('ins_session_full', $query_data);
        $this->db->executeAndGetResult('ins_session_full');

        $sess = new Session($query_data[':session_id']);

        return $query_data[':session_id'];
    }

    /**
     * @param string $user_id
     * @return bool
     * @throws Exception
     * @todo move this to separate mail sender
     */
    private function sendVerificationKey(string $user_id) : bool
    {
        $usr = new User($user_id, $this->db);
        $mail = new Mail($this->log, [ $this->mailAddresses['no-reply'], 'No Reply' ],
            [ [ $usr->data['user_email'], $usr->data['user_login'] ] ]);

        $mail->setMessage("Verification link",
            "<a href='http://rcse/test/verify.php?uid={$user_id}&key={$usr->generateVerificationKey()}'>Verify account</a>");
        return $mail->send();
    }

    /**
     * @param string $user_id
     * @return bool
     * @throws Exception
     * @todo move this to separate mail sender
     */
    private function sendPassRestoreKey(string $user_id) : bool
    {
        $usr = new User($user_id, $this->db);
        $mail = new Mail($this->log, [ $this->mailAddresses['no-reply'], 'No Reply' ],
            [ [ $usr->data['user_email'], $usr->data['user_login'] ] ]);

        $mail->setMessage("Pass Restore",
            "<a href='http://rcse/test/restore.html?uid={$user_id}&key={$usr->generateVerificationKey()}'>Change password</a>");
        return $mail->send();
    }
}