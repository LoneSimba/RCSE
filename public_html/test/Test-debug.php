<?php

use RCSE\Core\Control\Control;
use RCSE\Core\Database\Database;
use RCSE\Core\Secure\Authorization;
use RCSE\Core\User\User;
use RCSE\Core\Utils;

require_once '../vendor/autoload.php';

/* $selQuery = new \RCSE\Core\Database\SelectQuery('users', ['`*`']);
$insQuery = new \RCSE\Core\Database\InsertQuery('users', ['`user_id`', '`user_login`']);
$updQuery = new \RCSE\Core\Database\UpdateQuery('users', ['`user_login`', '`user_email`']);

print_r($selQuery->getStatement() . "</br>");
print_r($insQuery->getStatement() . "</br>");
print_r($updQuery->getStatement() . "</br>"); */
/*
$cont = new Control();
$db = new Database($cont);
$auth = new Authorization($db);

$user = new User('25972392-2b82-11eb-98fd-54ab3a8140ca', $db);*/

var_dump(Utils::getClientOS());