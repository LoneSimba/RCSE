<?php
declare(strict_types=1);

namespace RCSE\Core\Database;

class UpdateQuery extends Query
{
    protected function buildStatement()
    {
        $this->statement = "UPDATE `{$this->table}` SET ";

        $paramFields = str_replace("`", "", $this->fields);

        foreach($paramFields as $key => $val)
        {
            $this->statement .= "{$this->fields[$key]} = :{$val}";
            $this->statement .= ($key == count($this->fields) - 1) ? "" : ", ";
        }
    }
}