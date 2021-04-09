<?php

require "../vendor/autoload.php";
$log = new \RCSE\Core\Control\Log();
$db = new \RCSE\Core\Database\Database($log);
$auth = new \RCSE\Core\Secure\Authorization($db, $log);

$data = \RCSE\Core\Statics\GlobalArrays::getGetArray();
foreach ($data as &$val)
{
    $val = htmlspecialchars(trim($val));

}

print_r($auth->verifyAccount($data));