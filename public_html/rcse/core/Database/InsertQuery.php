<?php
declare(strict_types=1);

namespace RCSE\Core\Database;

class InsertQuery extends Query
{
    protected function buildStatement()
    {
        $this->statement = "INSERT INTO `{$this->table}`(". implode(", ", $this->fields) .") VALUES ";

        $paramFields = str_replace("`", "", $this->fields);

        foreach($paramFields as $key => $val)
        {
            $paramFields[$key] = ":{$val}";
        }

        $this->statement .= implode(", ", $paramFields);
    }
}