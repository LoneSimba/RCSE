<?php

use RCSE\Core\Control\Log;
use RCSE\Core\Database\Database;
use RCSE\Core\Secure\Authorization;

require_once '../vendor/autoload.php';

$data = \RCSE\Core\Statics\GlobalArrays::getPostArray();
$log = new Log();
$auth = new Authorization(new Database($log), $log);
$auth->register($data);

print_r(true);