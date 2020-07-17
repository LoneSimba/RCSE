<?php

declare(strict_types=1);

namespace RCSE\Core;

class Database
{

    private $control;
    private $pdo;
    private $query_list = [];

    public function __construct(Control $control) : void
    {
        $this->control = $control;
        $this->init();
    }

    private function init() : void
    {
        $conf = $this->control->getConfig('database');
        $dsn = 'mysql:host=' . $conf['host'] . ';port=' . $conf['port'] . ';dbname=' . $conf['name'];

        $this->control->log('Info', "Initializing Database connection (host: {$conf['host']}:{$conf['port']}, name: {$conf['name']}).", get_class($this));

        try {
            $this->pdo = new \PDO($dsn, $conf['user'], $conf['pass'], [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]);
        } catch (\PDOException $e) {
            $this->control->log('Fatal', "Failed to connect to database - {$e->getCode()}: {$e->getMessage()}.", get_class($this));
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        }

        $this->control->log('Info', "Database connected successfully.", get_class($this));
    }

    private function fillQueryList(): void
    {

    }

    private function validateData(array $data): bool
    {

        $keywords = ['OR', 'AND', 'SELECT', 'INSERT', 'CREATE', 'DELETE', 'UPDATE'];
        $contains = false;

        foreach ($data as $key => $value) {
            foreach ($keywords as $value1) {
                if ($value == $value1 && gettype($value) != 'boolean') {
                    $contains = true;
                    break;
                } else {
                    continue;
                }
            }
        }

        return $contains;
    }

    private function buildQuery(string $table, string $type, array $data = []): \PDOStatement
    {
        switch($type) {
            case 'select':
                return $this->buildQuery_Select($table, $data);
            break;
            case 'insert':
                return $this->buildQuery_Insert($table, $data);
            break;
            case 'update':
                return $this->buildQuery_Update($table, $data);
            break;
            case 'delete':
                //return $this->buildQuery_Delete($table, $data);
            break;
            default:
                $this->control->log('Error', "Unable to build query type '{$type}'", get_class($this));
                throw new \Exception("Unable to build query type '{$type}'", 000011);
            }

    }

    /**
     * buildQuery_Select builds and prepares SELECT statements with support of main additional args - WHERE, GROUP, ORDER, HAVING and LIMIT
     *
     * @param string $table Database table to be queried
     * @param array $data Data array, containing query elements in subarrays - 'required' contains rows names, 'conditions' contains conditions for WHERE arg, 'group' contains condts for GROUP BY, 'order' and ' limit' do the same for each of args
     * @return \PDOStatement Prepared PDO statement to be executed
     */
    private function buildQuery_Select(string $table, array $data = []): \PDOStatement
    {

        $query = "SELECT ";

        for ($i = 0; $i < count($data['required']); $i++) {
            $query .= "`{$data['required'][$i]}` ";
        }

        $query .= "FROM `{$table}` ";

        if (count($data['conditions']) != 0) {
            if (!$this->validateData($data['conditions'])) {
                $query .= "WHERE ";
                $item_count = 0;
                foreach ($data['conditions'] as $key => $value) {
                    $query .= "`{$key}` = :{$key}";
                    $item_count++;
                    if ($item_count < count($data['conditions'])) $query .= " AND ";
                }
            }
        }

        if(isset($data['group']) && !empty($data['group'])) {
            $query .= " GROUP BY {$data['group'][0]} ";
             if (isset($data['group']['asc'])) $query .= " ASC ";
             else if (isset($data['group']['desc'])) $query .= " DESC ";
             else $query .= "";
        }
        
        if(isset($data['order']) && !empty($data['order'])) {
            $query .= " ORDER BY {$data['order'][0]} ";
             if (isset($data['order']['asc'])) $query .= " ASC ";
             else if (isset($data['order']['desc'])) $query .= " DESC ";
             else $query .= "";
        }

        if(isset($data['limit']) && !empty($data['limit'])) {
            $query .= " LIMIT {$data['limit'][0]} ";
        }
        
        $query_prep = $this->pdo->prepare($query);
        return $query_prep;
    }

    private function buildQuery_Insert(string $table, array $data): \PDOStatement
    {

        $query = "INSERT INTO `{$table}` ";

        if (count($data) != 0) {
            if (!$this->validateData($data)) {
                if (gettype(current($data)) == 'array') {
                    $data_keys = array_keys(current($data));
                    $query .= "(";
                    for ($i = 0; $i < count($data_keys); $i++) {
                        $query .= "`{$data_keys[$i]}`";
                        $query .= ($i < count($data_keys) - 1) ? "," : ")";
                    }
                    $query .= " VALUES ";
                    for ($i = 0; $i < count($data); $i++) {
                        $query .= "(";
                        $item_count = 0;
                        foreach ($data[$i] as $key => $value) {
                            switch (gettype($value)) {
                                case 'integer':
                                case 'double':
                                    $query .= $value;
                                    break;
                                case 'string':
                                    $query .= "'{$value}'";
                                    break;
                                case 'boolean':
                                    $query .= (int) $value;
                                    break;
                            }
                            $item_count++;
                            $query .= ($item_count < count($data[$i])) ? "," : ")";
                        }
                        $query .= ($i < count($data) - 1) ? "," : "";
                    }
                } else {
                    $data_keys = array_keys($data);
                    $query .= "(";
                    for ($i = 0; $i < count($data_keys); $i++) {
                        $query .= "`{$data_keys[$i]}`";
                        $query .= ($i < count($data_keys) - 1) ? "," : ")";
                    }
                    $query .= " VALUES ";
                    $query .= "(";
                    $item_count = 0;
                    foreach ($data as $key => $value) {
                        switch (gettype($value)) {
                            case 'integer':
                            case 'double':
                                $query .= $value;
                                break;
                            case 'string':
                                $query .= "'{$value}'";
                                break;
                            case 'boolean':
                                $query .= (int) $value;
                                break;
                        }
                        $item_count++;
                        $query .= ($item_count < count($data)) ? "," : ")";
                    }
                }
            }
        }

        $query_prep = $this->pdo->prepare($query);
        return $query_prep;
    }

    private function buildQuery_Update(string $table, array $data): \PDOStatement
    {
        $query = "UPDATE `{$table}` SET ";

        if (count($data) != 0) {
            if (!$this->validateData($data['assigments'])) {
                $item_count = 0;
                foreach ($data['assigments'] as $key => $value) {
                    $query .= "`{$key}` = ";
                    switch (gettype($value)) {
                        case 'integer':
                        case 'double':
                            $query .= $value;
                            break;
                        case 'string':
                            $query .= "'{$value}'";
                            break;
                        case 'boolean':
                            $query .= (int) $value;
                            break;
                    }
                    $item_count++;
                    $query .= ($item_count < count($data['assigments'])) ? ", " : "";
                }
            }
            $item_count = 0;
            $query .= " WHERE ";
            foreach ($data['condition'] as $key => $value) {
                $query .= "`{$key}` = ";
                switch (gettype($value)) {
                    case 'integer':
                    case 'double':
                        $query .= $value;
                        break;
                    case 'string':
                        $query .= "'{$value}'";
                        break;
                    case 'boolean':
                        $query .= (int) $value;
                        break;
                }
                $item_count++;
                $query .= ($item_count < count($data['condition'])) ? " AND " : "";
            }
        }

        $query_prep = $this->pdo->prepare($query);
        return $query_prep;
    }

    //TBD
    /*private function buildQuery_Delete(string $table, array $data): \PDOStatement
    {}*/

    private function executeQuery(\PDOStatement $query): array
    {
    }

    public function getData(string $table, string $type, array $required, array $data = []): array
    {
    }
}
