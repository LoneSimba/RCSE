<?php
declare(strict_types=1);

namespace RCSE\Core\User;

use RCSE\Core\Database\Database;

class UserSession
{
    public array $data = [];
    private Database $db;


    public function __construct(string $id, Database $db)
    {
        $this->db = $db;
        $this->getData($id);
    }

    private function getData(string $id)
    {
        $this->db->addQueryData('sel_session_by_id', [':id'=>$id]);
        $this->data = $this->db->executeAndGetResult('sel_session_by_id');
    }
}