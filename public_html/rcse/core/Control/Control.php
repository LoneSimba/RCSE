<?php
declare(strict_types=1);

namespace RCSE\Core\Control;

class Control
{
    public Log $log;
    public Config $config;
    //public $permissions;

    public function __construct() 
    {
        $this->log = new Log();
        $this->config = new Config();
        //$this->permission = new Permissions($this);
    }
}