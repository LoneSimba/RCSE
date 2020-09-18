<?php
declare(strict_types=1);

namespace RCSE\Core\Database;
use RCSE\Core\Control\Control;

class Database
{

    private $control;
    private $dbh;
    private $conf;
    private $queryList = [];

    public function __construct(Control $control)
    {
        $this->control = $control;
        $this->init();
    }

    /**
     * Executes specified query and returns fetched array
     *
     * @param string $queryName Query to execute
     * @return array Resulting array
     */
    public function executeAndGetResult(string $queryName) : array
    {
        $data = $this->queryList[$queryName]->execute()->fetchDataArray();
        return $data->result;
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
     * Used to initialize class. Perhaps, could be moved to constructor
     *
     * @return void
     */
    private function init() : void
    {
        $this->conf = $this->control->config->getConfig('database');
        $this->connectDatabase();
        $this->fillQueryList();
    }

    /**
     * Connects to database using config data
     *
     * @throws Exception In case of failure
     * @return void
     */
    private function connectDatabase(): void
    {
        $dsn = 'mysql:host=' . $this->conf['host'] . ';port=' . $this->conf['port'] . ';dbname=' . $this->conf['name'];

        $this->control->log->log('Info', "Initializing Database connection (host: {$this->conf['host']}:{$this->conf['port']}, name: {$this->conf['name']}).", get_class($this));

        try {
            $this->dbh = new \PDO($dsn, $this->conf['user'], $this->conf['pass'], [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]);
        } catch (\PDOException $e) {
            $this->control->log->log('Fatal', "Failed to connect to database - {$e->getCode()}: {$e->getMessage()}.", get_class($this));
            throw new \Exception($e->getMessage(), 0x000200);
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
        $this->query_list['sel_user_all'] = (new SelectQuery('users', ['`*`']))->prepare($this->dbh);
        $this->query_list['sel_user_full_by_id'] = (new SelectQuery('users', ['`*`']))->addWhere(['`user_id`'=>':id'])->prepare($this->dbh);
        $this->query_list['sel_user_creditans_by_login'] = (new SelectQuery('users', ['`user_id`', '`user_login`', '`user_email`', '`user_passhash`']))->addWhere(['`user_login`'=>':login'])->prepare($this->dbh);
        $this->query_list['sel_user_creditans_by_id'] = (new SelectQuery('users', ['`user_id`', '`user_login`', '`user_email`', '`user_passhash`']))->addWhere(['`user_id`'=>':id'])->prepare($this->dbh);
        $this->query_list['sel_user_creditans_by_email'] = (new SelectQuery('users', ['`user_id`', '`user_login`', '`user_email`', '`user_passhash`']))->addWhere(['`user_email`'=>':email'])->prepare($this->dbh);
        $this->query_list['upd_user_full_by_id'] = (new UpdateQuery('users', ['`user_login`', '`user_email`', '`user_passhash`', '`group_id`', '`user_bdate`', '`user_avatar`', '`user_prefs`', '`user_perms`']))->addWhere(['`user_id`' => ':id'])->prepare($this->dbh);
        $this->query_list['upd_user_creditans_by_id'] = (new UpdateQuery('users', ['`user_login`', '`user_email`', '`user_passhash`', '`group_id`', '`user_bdate`', '`user_avatar`', '`user_prefs`', '`user_perms`']))->addWhere(['`user_id`' => ':id'])->prepare($this->dbh);
        $this->query_list['ins_user_full'] = (new InsertQuery('users', ['`user_login`', '`user_email`', '`user_passhash`', '`group_id`', '`user_bdate`', '`user_regdate`']));

        
    }

}
