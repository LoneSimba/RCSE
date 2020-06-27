<?php
declare(strict_types=1);
namespace RCSE\Core;

class Database {

    private $control;
    private $pdo;

    public function __construct(Control $control) {
        $this->control = $control;
        $this->init();
    }

    private function init() {
        $conf = $this->control->getConfig('database');
        $dsn = 'mysql:host=' .$conf['host']. ';port=' .$conf['port']. ';dbname=' .$conf['name'];

        $this->control->log('Info', "Initializing Database connection (host: {$conf['host']}:{$conf['port']}, name: {$conf['name']}).", get_class($this));
        
        try {
            $this->pdo = new \PDO($dsn, $conf['login'], $conf['passw'], [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]);
        } catch (\PDOException $e) {
            $this->control->log('Fatal', "Failed to connect to database - {$e->getCode()}: {$e->getMessage()}.", get_class($this));
            throw new \Exception($e->getMessage(), (int)$e->getCode());
        }

        $this->control->log('Info', "Database connected successfully.", get_class($this));
    }

    private function validateData(array $data) : bool {

        $keywords = ['OR', 'AND', 'SELECT', 'INSERT', 'CREATE', 'DELETE', 'UPDATE'];
        $contains = false;

        foreach($data as $key => $value) {
            foreach($keywords as $value1) {
                if($value == $value1) {
                    $contains = true;
                    break;
                } else {
                    continue;
                }
            }
        }

        return $contains;
    }

    private function buildQuery(string $table, string $type, array $required, array $data = []) : \PDOStatement {


    }

    private function buildQuery_Select(string $table, array $required, array $data = []) : \PDOStatement {

        $query = "SELECT ";

        for($i = 0; $i < count($required); $i++) {
            $query .= "`{$required[$i]}` ";
        }

        $query .= "FROM `{$table}` ";

        if(count($data) != 0) {
            if(!$this->validateData($data)) {
                $query .= "WHERE ";
                $item_count = 0;
                foreach($data as $key => $value) {
                    $query .= "`{$key}` = ";
                    switch(gettype($value)) {
                        case 'integer':
                        case 'double':
                            $query .= $value;
                            break;
                        case 'string':
                            $query .= "'{$value}' ";
                            break;
                        case 'boolean':
                            $query .= (integer)$value;
                            break;
                    }
                    $item_count++;
                    if($item_count < count($data)) $query .= "AND ";
                }
            }
        }

        $query_prep = $this->pdo->prepare($query);
        return $query_prep;
    }

    private function buildQuery_Insert(string $table, array $data) : \PDOStatement {

        $query = "INSERT INTO `{$table}` ";

        if(count($data) != 0) {
            if(!$this->validateData($dat)) {
                if(gettype($data[0]) == 'array') {
                    $data_keys = array_keys($data);
                    $query .= "(";
                    for($i = 0; $i < count($data_keys); $i++) {
                        $query .= "`{$data_keys[$i]}`";
                        $query .= ($i<count($data_keys)-1)? "," : ")";
                    }
                }
            }
        }
    }

    private function executeQuery(\PDOStatement $query) : array {}

    public function getData(string $table, string $type, array $required, array $data = []) : array {
        
    }
}