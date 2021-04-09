<?php


namespace RCSE\Core\Database;


class DeleteQuery extends Query
{

    public function __construct(string $_table, array $_fields = [])
    {
        parent::__construct($_table, $_fields);
    }

    protected function buildStatement(): void
    {
        $this->statement = "DELETE FROM `{$this->table}`";
    }
}