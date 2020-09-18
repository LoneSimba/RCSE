<?php
require_once '../vendor/autoload.php';

$selQuery = new \RCSE\Core\Database\SelectQuery('users', ['`*`']);
$insQuery = new \RCSE\Core\Database\InsertQuery('users', ['`user_id`', '`user_login`']);
$updQuery = new \RCSE\Core\Database\UpdateQuery('users', ['`user_login`', '`user_email`']);

print_r($selQuery->getStatement() . "</br>");
print_r($insQuery->getStatement() . "</br>");
print_r($updQuery->getStatement() . "</br>");