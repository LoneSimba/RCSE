<?php
declare(strict_types=1);

namespace RCSE\Core\Database;

class InsertQuery extends Query
{
    protected function buildStatement(): void
    {
        $this->statement = "INSERT INTO `{$this->table}`(". implode(", ", $this->fields) .") VALUES (";

        $paramFields = str_replace("`", "", $this->fields);

        foreach($paramFields as $key => $val)
        {
            $paramFields[$key] = ":{$val}";
        }

        $this->statement .= implode(", ", $paramFields);
        $this->statement .= ")";
    }

    public function addUpdate(): self
    {
        $string = " AS new ON DUPLICATE KEY UPDATE ";

        foreach($this->fields as $key => $val)
        {
            $string .= " {$val} = new.{$val}";
            if ($key != count($this->fields) - 1) {
                $string .= ",";
            }
        }

        $this->statement .= $string;
        return $this;
    }

    public function addWhere(array $data, bool $shouldBeEqual = true, bool $disjunctive = false): Query
    {
        return $this;
    }
}