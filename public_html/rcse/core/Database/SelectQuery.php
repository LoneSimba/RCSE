<?php
declare(strict_types=1);

namespace RCSE\Core\Database;

class SelectQuery extends Query
{
    protected function buildStatement() : void
    {
        $this->statement = "SELECT ". implode(", ", $this->fields) ." FROM `{$this->table}`";
    }
}