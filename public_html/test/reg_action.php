<?php

use RCSE\Core\Control\Control;
use RCSE\Core\Database\Database;
use RCSE\Core\Secure\Authorization;

require_once '../vendor/autoload.php';

$data = $_POST;
$auth = new Authorization(new Database(new Control()));
$auth->register($data);