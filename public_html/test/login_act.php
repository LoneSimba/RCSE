<?php
require ('../vendor/autoload.php');

$reqData = \RCSE\Core\Statics\GlobalArrays::getPostArray();
$log = new \RCSE\Core\Control\Log();
$auth = new \RCSE\Core\Secure\Authorization(new \RCSE\Core\Database\Database($log), $log);

switch($reqData['type']) {
    case 'login1F':
        echo $auth->login1F(['login' => $reqData['login'], 'password' => $reqData['password']]);
        break;
    case 'login2F':
        echo $auth->login2F(['login' => $reqData['login'], 'key' => $reqData['key']]);
        break;
}