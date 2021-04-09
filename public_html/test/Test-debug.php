<?php

require_once '../vendor/autoload.php';

use RCSE\Core\Control\Config;
use RCSE\Core\Control\Log;
use RCSE\Core\Database\Database;
use RCSE\Core\Secure\Authorization;
use RCSE\Core\Statics\Utils;

$log = new Log();
$db = new Database($log);
$auth = new Authorization($db, $log);

var_dump((new Config())->getConfig("mailer"));

//var_dump($auth->register(['login'=>'lone', 'password'=>'test', 'bdate'=>'1999-11-04', 'email'=>'siluet-stalker99@yandex.ru']));