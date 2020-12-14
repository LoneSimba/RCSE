<?php
declare(strict_types=1);

namespace RCSE\Core\Database;
use PDO;
use PDOException;
use Exception;
use RCSE\Core\Control\Config;
use RCSE\Core\Control\Log;

class Database
{
    private Config $config;
    private PDO $dbh;
    private Log $log;
    private array $conf;
    private array $queryList;

    public function __construct()
    {
        $this->config = new Config();
        $this->log = new Log();
        $this->conf = $this->config->getConfig('database');
        $this->connectDatabase();
        $this->fillQueryList();
    }

    /**
     * Passthrough for $queryName's addData function
     *
     * @param string $queryName Target query
     * @param array $data Data to add
     * @return void
     */
    public function addQueryData(string $queryName, array $data) : void
    {
        $this->queryList[$queryName]->addData($data);
    }

    /**
     * Executes specified built-in query and returns fetched array
     *
     * @param string $queryName Query to execute
     * @return array Resulting array
     * @throws Exception
     */
    public function executeAndGetResult(string $queryName) : array
    {
        $this->queryList[$queryName]->execute()->fetchDataArray();
        return $this->queryList[$queryName]->result;
    }

    /**
     * Executes custom query
     *
     * @param Query $query
     * @return array
     * @throws Exception
     */
    public function executeCustomQuery(Query $query) : array
    {
        $query->prepare($this->dbh)->execute()->fetchDataArray();
        return $query->result;
    }

    /**
     * Connects to database using config data
     *
     * @throws Exception
     * @todo Should throw DatabaseConnectionException
     * @return void
     */
    private function connectDatabase(): void
    {
        $dsn = 'mysql:host=' . $this->conf['host'] . ';port=' . $this->conf['port'] . ';dbname=' . $this->conf['name'];

        $this->log->log('Info',
            "Initializing Database connection (host: {$this->conf['host']}:{$this->conf['port']}, name: {$this->conf['name']}).",
            self::class);

        try {
            $this->dbh = new PDO($dsn, $this->conf['user'], $this->conf['pass'],
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]);
        } catch (PDOException $e) {
            $this->log->log('Fatal',
                "Failed to connect to database - {$e->getCode()}: {$e->getMessage()}.",
                self::class);
            throw new Exception($e->getMessage(), 0x000200);
        }

        $this->log->log('Info', "Database connected successfully.", self::class);
    }

    /**
     * Fills $queryList
     *
     * @return void
     */
    private function fillQueryList(): void
    {
        $this->queryList['sel_user_all'] = (new SelectQuery('users', ['`*`']))->prepare($this->dbh);
        $this->queryList['sel_user_safe_by_id'] = (new SelectQuery('users',
            ['`user_id`', '`user_login`', '`user_email`', '`group_id`', '`user_regdate`', '`user_bdate`', '`user_avatar`', '`user_prefs`', '`user_perms`']))
            ->addWhere(['`user_id`'=>':user_id'])->prepare($this->dbh);
        $this->queryList['sel_user_safe_by_email'] = (new SelectQuery('users',
            ['`user_id`', '`user_login`', '`user_email`', '`group_id`', '`user_regdate`', '`user_bdate`', '`user_avatar`', '`user_prefs`', '`user_perms`']))
            ->addWhere(['`user_email`'=>':user_email'])->prepare($this->dbh);
        $this->queryList['sel_user_safe_by_login'] = (new SelectQuery('users',
            ['`user_id`', '`user_login`', '`user_email`', '`group_id`', '`user_regdate`', '`user_bdate`', '`user_avatar`', '`user_prefs`', '`user_perms`']))
            ->addWhere(['`user_login`'=>':user_login'])->prepare($this->dbh);
        $this->queryList['upd_user_safe_by_id'] = (new UpdateQuery('users',
            ['`group_id`', '`user_bdate`', '`user_avatar`', '`user_prefs`', '`user_perms`']))
            ->addWhere(['`user_id`' => ':user_id'])->prepare($this->dbh);
        $this->queryList['upd_user_credentials_by_id'] = (new UpdateQuery('users',
            ['`user_login`', '`user_email`', '`user_passhash`']))
            ->addWhere(['`user_id`' => ':user_id'])->prepare($this->dbh);
        $this->queryList['ins_user_full'] = (new InsertQuery('users',
            ['`user_id`', '`user_login`', '`user_email`', '`user_passhash`', '`group_id`', '`user_bdate`', '`user_regdate`', '`user_prefs`', '`user_perms`']))
            ->prepare($this->dbh);
    
        $this->queryList['sel_group_all'] = (new SelectQuery('groups', ['`*`']))->prepare($this->dbh);
        $this->queryList['sel_group_by_id'] = (new SelectQuery('groups', ['`*`']))
            ->addWhere(['`group_id`'=>':id'])->prepare($this->dbh);
        $this->queryList['sel_group_id_by_name'] = (new SelectQuery('groups', ['`group_id`']))
            ->addWhere(['`group_title`'=>':title'])->prepare($this->dbh);
    
        $this->queryList['sel_session_by_id'] = (new SelectQuery('sessions', ['`*`']))
            ->addWhere(['`session_id`'=>':session_id'])->prepare($this->dbh);
        $this->queryList['ins_session_full'] = (new InsertQuery('sessions',
            ['`session_id`', '`user_id`', '`session_ips`', '`session_start`', '`session_browser`', '`session_os`']))
            ->prepare($this->dbh);
        $this->queryList['upd_session_by_id'] = (new UpdateQuery('sessions', ['`session_ips`', '`session_end`']))
            ->addWhere(['`session_id`'=>':session_id'])->prepare($this->dbh);

        $this->queryList['sel_auth_key_by_user_id'] = (new SelectQuery('auth_keys', ['`*`']))
            ->addWhere(['`user_id`'=>':user_id'])->prepare($this->dbh);
        $this->queryList['ins_auth_key_full'] = (new InsertQuery('auth_keys', ['`key_id`', '`user_id`', '`key_expires`']))
            ->prepare($this->dbh);

    }

}
