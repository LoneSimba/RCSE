<?php
declare(strict_types=1);

namespace RCSE\Core\Database;

class SelectQuery extends Query
{
    protected function buildStatement()
    {
        $this->statement = "SELECT ". join(", ", $this->fields) ." FROM `{$this->table}`";
    }
}