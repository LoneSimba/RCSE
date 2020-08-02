<?php

declare(strict_types=1);

namespace RCSE\Core\Database;

class QueryBuilder
{


    /**
     * buildQuery_Select builds and prepares SELECT statements with support of main additional args - WHERE, GROUP, ORDER, HAVING and LIMIT
     *
     * @param string $table Database table to be queried
     * @param array $data Data array, containing query elements in subarrays - 'required' contains rows names, 'conditions' contains conditions for WHERE arg, 'group' contains condts for GROUP BY, 'order' and ' limit' do the same for each of args
     * @return \PDOStatement Prepared PDO statement to be executed
     */
    public function buildQuery_Select(string $table, array $keys, array $where = null, string $by_cond = 'none', array $group_key = [], array $order = [], int $limit = 0): \PDOStatement
    {

        $query = "SELECT ";

        if (!empty($keys)) {
            foreach ($keys as $key => $value) {
                $query .= ($key == count($keys) - 1) ? "`{$value}` " : "`{$value}`, ";
            }
        } else {
            $query .= "* ";
        }

        $query .= "FROM `{$table}` ";

        if ($where != null && !empty($where)) {
            $query .= " WHERE ";
            foreach ($where as $key => $value) {
                $query .= ($key == count($keys) - 1) ? "`{$value}` = :{$value} AND " : "`{$value}` = :{$value}";
            }
        }

        if ($by_cond == 'order' && !empty($data['order'])) {
            $query .= " ORDER BY ";
            foreach($order as $key => $value) {
                
            }
            if (isset($data['order']['asc'])) $query .= " ASC ";
            else if (isset($data['order']['desc'])) $query .= " DESC ";
            else $query .= "";
        }

        if (isset($data['limit']) && !empty($data['limit'])) {
            $query .= " LIMIT {$data['limit'][0]} ";
        }

        $query_prep = $this->pdo->prepare($query);
        return $query_prep;
    }

    public function buildQuery_Insert(string $table, array $data): \PDOStatement
    {

        $query = "INSERT INTO `{$table}`(";

        foreach ($data as $key => $value) {
            $query .= ($key == count($data) - 1) ? "`{$value}`) VALUES (" : "`{$value}`, ";
        }

        foreach ($data as $key => $value) {
            $query .= ($key == count($data) - 1) ? ":{$value})" : ":{$value}, ";
        }

        return $this->pdo->prepare($query);
    }

    public function buildQuery_Update(string $table, array $data): \PDOStatement
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
    /*public function buildQuery_Delete(string $table, array $data): \PDOStatement
    {}*/
}
