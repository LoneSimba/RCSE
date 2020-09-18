<?php
declare(strict_types=1);

namespace RCSE\Core\Database;

abstract class Query
{
    public $result = [];
    protected $statement = "";
    protected $pdoStatement;
    protected $data = [];
    protected $fields = [];
    protected $table = "";

    public function __construct(string $table, array $fields)
    {
        $this->addField($fields);
        $this->setTable($table);
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
     * @param boolean $shouldBeEqueal Either fields should be equeal to provided data
     * @param boolean $disjunctive Should be multiple fields be true at the same time
     * @return Query
     */
    public function addWhere(array $data, bool $shouldBeEqueal = true, bool $disjunctive = false) : Query
    {
        $compSign = ($shouldBeEqueal) ? " = " : " != ";
        $separator = ($disjunctive) ? " OR " : " AND ";
        
        if ($this->statement == '') 
        {
            $this->buildStatement();
        }

        $string = " WHERE ";
        $counter = 0;
        foreach($data as $key => $val)
        {
            $string .= "`{$key}`{$compSign}{$val}";
            if($counter < count($data)-1) $string .= "{$separator}";
        }

        $this->statement .= $string;
        return $this;
    }

    /**
     * Prepares an PDOStatement using provided database handler
     *
     * @param \PDO $dbh PDO database handler
     * @return Query
     */
    public function prepare(\PDO $dbh) : Query
    {
        if(empty($this->statement)) $this->buildStatement();
        $this->pdoStatement = $dbh->prepare($this->statement);
        return $this;
    }

    /**
     * Executes prepared statement, if statement has not been built or prepared throws an exception.
     *
     * @return void
     */
    public function execute() 
    {
        if(empty($this->statement) || empty($this->pdoStatement)) throw new \Exception("Failed to execute statement - statement has not been built or prepared.", 0x000203);
        $this->bindData();
        $this->pdoStatement->execute();
    }

    /**
     * Fetches the result of query execution and puts it into $result array
     *
     * @return void
     */
    public function fetchDataArray() { $this->result = $this->pdoStatement->fetchAll(); }

    public function getStatement() { return $this->statement; }
    
    protected function addField(array $fields) { $this->fields = array_merge($this->fields, $fields); }

    protected function setTable(string $table) { $this->table = $table; }

    protected function bindData()
    {
        if(array_intersect_key($this->data, array_flip($this->fields))) {
            foreach($this->data as $key => $value) {
                $this->pdoStatement->bindParam($key, $value);
            }
        } else {
            throw new \Exception("Failed to bind data - keys does not match", 0x000202);
        }
    }
    
    protected abstract function buildStatement();
}