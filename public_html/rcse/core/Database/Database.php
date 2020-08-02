<?php

declare(strict_types=1);

namespace RCSE\Core\Database;

class Database
{

    private $control;
    private $query_builder;
    private $pdo;
    private $conf;
    private $query_list = [];

    public function __construct(\RCSE\Core\Control $control)
    {
        $this->control = $control;
        $this->query_builder = new QueryBuilder();
        $this->init();
    }

    private function init() : void
    {
        $this->conf = $this->control->getConfig('database');
        $this->connectDatabase();
        $this->fillQueryList();
    }

    private function connectDatabase(): void
    {
        $dsn = 'mysql:host=' . $this->conf['host'] . ';port=' . $this->conf['port'] . ';dbname=' . $this->conf['name'];

        $this->control->log('Info', "Initializing Database connection (host: {$conf['host']}:{$conf['port']}, name: {$conf['name']}).", get_class($this));

        try {
            $this->pdo = new \PDO($dsn, $this->conf['user'], $this->conf['pass'], [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]);
        } catch (\PDOException $e) {
            $this->control->log('Fatal', "Failed to connect to database - {$e->getCode()}: {$e->getMessage()}.", get_class($this));
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        }

        $this->control->log('Info', "Database connected successfully.", get_class($this));
    }

    private function fillQueryList(): void
    {
        $this->query_list[''] = $this->buildQuery();
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

    

    private function executeQuery(\PDOStatement $query): array
    {
    }

    public function getData(string $table, string $type, array $required, array $data = []): array
    {
    }
}
