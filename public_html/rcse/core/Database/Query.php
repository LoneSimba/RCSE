<?php
declare(strict_types=1);

namespace RCSE\Core\Database;

use Exception;
use PDO;
use PDOStatement;

abstract class Query
{
    public array $result;

    protected PDOStatement $pdoStatement;
    protected string $statement;
    protected string $table;
    protected array $data = [];
    protected array $fields = [];

    /**
     * Query constructor.
     * @param string $_table Table to interact
     * @param array $_fields Fields to interact
     */
    public function __construct(string $_table, array $_fields)
    {
        $this->addField($_fields);
        $this->setTable($_table);
        $this->buildStatement();
    }

    /**
     * Adds $data to inner array, should be associative with keys representing fields
     *
     * @param array $data Array of data to add with keys representing fields
     * @return self
     */
    public function addData(array $data): self
    { 
        $this->data = array_merge($this->data, $data); 
        return $this;
    }

    /**
     * Add 'WHERE' part to statement. Notice - should be called before calling prepare().
     *
     * @param array $data Array of data with keys representing table fields
     * @param bool $shouldBeEqual Either fields should be equal to provided data
     * @param bool $disjunctive Should be multiple fields be true at the same time
     * @return self
     *@todo Should think of way to provide multiple types of comparison and separators
     */
    public function addWhere(array $data, bool $shouldBeEqual = true, bool $disjunctive = false): self
    {
        $compSign = ($shouldBeEqual) ? "=" : "!=";
        $separator = ($disjunctive) ? " OR " : " AND ";

        $string = " WHERE ";
        $counter = 0;
        foreach($data as $key => $val)
        {
            if (strpos($val, ":") === false) $val = "'{$val}'";
            $string .= "{$key}{$compSign}{$val}";
            if (!($counter == count($data)-1)) $string .= "{$separator}";
            $counter++;
        }

        $this->statement .= $string;
        return $this;
    }

    /**
     * Executes prepared statement, if statement has not been built or prepared throws an exception.
     *
     * @param PDO $dbh
     * @return self
     */
    public function execute(PDO $dbh): self
    {
        $this->pdoStatement = $dbh->prepare($this->statement);
        $this->bindData();
        $this->pdoStatement->execute();

        return $this;
    }

    /**
     * Fetches the result of query execution and puts it into $result array
     *
     * @return void
     * @todo Should throw QueryExecutionFailure
     * @throws Exception
     */
    public function fetchDataArray(): void
    {
        if (is_null($this->getErrorMessage()[1])) {
            $this->result = ($this->pdoStatement->columnCount() > 0) ? $this->pdoStatement->fetchAll(PDO::FETCH_ASSOC) : [];
        } else {
            throw new Exception("Query execution resulted in following error: {$this->getErrorMessage()[2]}", 0x000204);
        }
    }

    /**
     * Returns current statement
     *
     * @return string
     */
    public function getStatement(): string { return $this->statement; }

    /**
     * Returns errorInfo array for executed pdoStatement
     *
     * @return array
     */
    public function getErrorMessage(): array { return $this->pdoStatement->errorInfo(); }
    
    protected function addField(array $fields): void { $this->fields = array_merge($this->fields, $fields); }

    protected function setTable(string $table): void { $this->table = $table; }

    protected function bindData(): void
    {
        foreach ($this->data as $key => $value) {
            $this->pdoStatement->bindValue($key, $value);
        }
    }
    
    abstract protected function buildStatement(): void;
}