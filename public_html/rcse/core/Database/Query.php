<?php
declare(strict_types=1);

namespace RCSE\Core\Database;

use Exception;
use PDO;
use PDOStatement;

abstract class Query
{
    public $result;
    protected string $statement;
    protected PDOStatement $pdoStatement;
    protected array $data = [];
    protected array $fields = [];
    protected string $table;

    public function __construct(string $_table, array $_fields)
    {
        $this->addField($_fields);
        $this->setTable($_table);
        $this->buildStatement();
    }

    /**
     * Adds $data to inner array, should be associative with keys representing fields
     *
     * @todo Should check data for embedded statements
     * @param array $data Array of data to add with keys representing fields
     * @return Query
     */
    public function addData(array $data) : Query 
    { 
        $this->data = array_merge($this->data, $data); 
        return $this;
    }

    /**
     * Add 'WHERE' part to statement. Notice - should be called before calling prepare().
     *
     * @todo Should think of way to provide multiple types of comparison and separators
     * @param array $data Array of data with keys representing table fields
     * @param boolean $shouldBeEqual Either fields should be equeal to provided data
     * @param boolean $disjunctive Should be multiple fields be true at the same time
     * @return Query
     */
    public function addWhere(array $data, bool $shouldBeEqual = true, bool $disjunctive = false) : Query
    {
        $compSign = ($shouldBeEqual) ? " = " : " != ";
        $separator = ($disjunctive) ? " OR " : " AND ";
        
        if ($this->statement == '') 
        {
            $this->buildStatement();
        }

        $string = " WHERE ";
        $counter = 0;
        foreach($data as $key => $val)
        {
            $string .= "{$key}{$compSign}{$val}";
            if($counter < count($data)-1) $string .= "{$separator}";
        }

        $this->statement .= $string;
        return $this;
    }

    /**
     * Prepares an PDOStatement using provided database handler
     *
     * @param PDO $dbh PDO database handler
     * @return Query
     */
    public function prepare(PDO $dbh) : Query
    {
        if(empty($this->statement)) $this->buildStatement();
        $this->pdoStatement = $dbh->prepare($this->statement);
        return $this;
    }

    /**
     * Executes prepared statement, if statement has not been built or prepared throws an exception.
     *
     * @return Query
     * @throws Exception
     */
    public function execute() 
    {
        if(empty($this->statement) || empty($this->pdoStatement)) throw new Exception("Failed to execute statement - statement has not been built or prepared.", 0x000203);
        $this->bindData();
        $this->pdoStatement->execute();

        return $this;
    }

    /**
     * Fetches the result of query execution and puts it into $result array
     *
     * @return void
     */
    public function fetchDataArray() {
        if(is_null($this->getErrorMessage()[1])) {
            $this->result = ($this->pdoStatement->columnCount() > 0) ? $this->pdoStatement->fetchAll(PDO::FETCH_ASSOC) : [];
        }
        else
        {
            $this->result = false;
        }
    }

    public function getStatement() { return $this->statement; }

    public function getErrorMessage() { return $this->pdoStatement->errorInfo(); }
    
    protected function addField(array $fields) { $this->fields = array_merge($this->fields, $fields); }

    protected function setTable(string $table) { $this->table = $table; }

    protected function bindData()
    {
        foreach($this->data as $key => $value) {
            $this->pdoStatement->bindValue($key, $value);
        }
    }
    
    protected abstract function buildStatement();
}