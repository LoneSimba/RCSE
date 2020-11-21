<?php
declare(strict_types=1);

namespace RCSE\Core\Database;
use PDO;
use PDOException;
use Exception;
use RCSE\Core\Control\Control;

class Database
{

    private Control $control;
    private PDO $dbh;
    private array $conf;
    private array $queryList;

    public function __construct(Control $control)
    {
        $this->control = $control;
        $this->conf = $this->control->config->getConfig('database');
        $this->connectDatabase();
        $this->fillQueryList();
    }

    /**
     * Executes specified built-in query and returns fetched array
     *
     * @param string $queryName Query to execute
     * @return array|boolean Resulting array or false in case of failure
     */
    public function executeAndGetResult(string $queryName)
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
        $query->prepare($this->dbh)->execute();
        $query->fetchDataArray();

        return $query->result;
    }

    /**
     * Passthrough for $queryName's addData function
     *
     * @param string $queryName Target query
     * @param array $data Data to add
     * @return void
     */
    public function addQueryData(string $queryName, array $data)
    {
        $this->queryList[$queryName]->addData($data);
    }

    /**
     * Connects to database using config data
     *
     * @throws Exception
     * @return void
     */
    private function connectDatabase(): void
    {
        $dsn = 'mysql:host=' . $this->conf['host'] . ';port=' . $this->conf['port'] . ';dbname=' . $this->conf['name'];

        $this->control->log->log('Info', "Initializing Database connection (host: {$this->conf['host']}:{$this->conf['port']}, name: {$this->conf['name']}).", get_class($this));

        try {
            $this->dbh = new PDO($dsn, $this->conf['user'], $this->conf['pass'], [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]);
        } catch (PDOException $e) {
            $this->control->log->log('Fatal', "Failed to connect to database - {$e->getCode()}: {$e->getMessage()}.", get_class($this));
            throw new Exception($e->getMessage(), 0x000200);
        }

        $this->control->log->log('Info', "Database connected successfully.", get_class($this));
    }

    /**
     * Fills $queryList
     *
     * @return void
     */
    private function fillQueryList(): void
    {
        $this->queryList['sel_user_all'] = (new SelectQuery('users', ['`*`']))->prepare($this->dbh);
        $this->queryList['sel_user_full_by_id'] = (new SelectQuery('users', ['`*`']))->addWhere(['`user_id`'=>':id'])->prepare($this->dbh);
        $this->queryList['upd_user_full_by_id'] = (new UpdateQuery('users', ['`user_login`', '`user_email`', '`user_passhash`', '`group_id`', '`user_bdate`', '`user_avatar`', '`user_prefs`', '`user_perms`']))->addWhere(['`user_id`' => ':id'])->prepare($this->dbh);
        $this->queryList['upd_user_credentials_by_id'] = (new UpdateQuery('users', ['`user_login`', '`user_email`', '`user_passhash`', '`group_id`', '`user_bdate`', '`user_avatar`', '`user_prefs`', '`user_perms`']))->addWhere(['`user_id`' => ':id'])->prepare($this->dbh);
        $this->queryList['ins_user_full'] = (new InsertQuery('users', ['`user_id`', '`user_login`', '`user_email`', '`user_passhash`', '`group_id`', '`user_bdate`', '`user_regdate`', '`user_prefs`', '`user_perms`']))->prepare($this->dbh);
    
        $this->queryList['sel_group_all'] = (new SelectQuery('groups', ['`*`']))->prepare($this->dbh);
        $this->queryList['sel_group_by_id'] = (new SelectQuery('groups', ['`*`']))->addWhere(['`group_id`'=>':id'])->prepare($this->dbh);
        $this->queryList['sel_group_id_by_name'] = (new SelectQuery('groups', ['`group_id`']))->addWhere(['`group_title`'=>':title'])->prepare($this->dbh);
    
        $this->queryList['sel_session_by_id'] = (new SelectQuery('sessions', ['`*`']))->addWhere(['`session_id`'=>':id'])->prepare($this->dbh);
    }

}
