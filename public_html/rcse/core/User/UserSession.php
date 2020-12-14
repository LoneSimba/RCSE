<?php
declare(strict_types=1);

namespace RCSE\Core\User;

use RCSE\Core\Database\Database;
use RCSE\Core\Utils;

class UserSession
{
    public array $data = [];
    private array $ips = [];
    private Database $db;


    public function __construct(string $id, Database $db)
    {
        $this->db = $db;
        $this->getData($id);
    }

    public function saveData() : void
    {
        $query_data = [];
        foreach ($this->data as $key => $val)
        {
            switch ($key)
            {
                case 'session_id':
                case 'user_id':
                case 'session_geo':
                case 'session_start':
                case 'session_end':
                case 'session_browser':
                case 'session_os':
                    $query_data[':{$key}'] = $val;
                    break;
                case 'session_ips':
                    $query_data[':{$key}'] = json_encode($this->ips);
                    break;
            }
        }

        $this->db->addQueryData('upd_session_by_id', $query_data);
        $this->db->executeAndGetResult('upd_session_by_id');
    }

    public function endNow() : void
    {
        $this->data['session_end'] = Utils::getTimestamp();
        $this->saveData();
    }

    private function getData(string $id) : void
    {
        $this->db->addQueryData('sel_session_by_id', [':id'=>$id]);
        $this->data = $this->db->executeAndGetResult('sel_session_by_id');
    }
}