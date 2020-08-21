<?php
declare(strict_types=1);

namespace RCSE\Core\Control;

class Control
{
    public $log;
    public $config;

    public function __construct() 
    {
        $this->log = new Log();
        $this->config = new Config();
    }
}