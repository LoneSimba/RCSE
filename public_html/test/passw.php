<?php
require ('../vendor/autoload.php');

$reqData = \RCSE\Core\Statics\GlobalArrays::getPostArray();
$log = new \RCSE\Core\Control\Log();
$auth = new \RCSE\Core\Secure\Authorization(new \RCSE\Core\Database\Database($log), $log);

switch($reqData['type'])
{
    case "req":
        echo $auth->requireRestorePassword($reqData['login']);
        break;
    case "res":
        unset($reqData['type']);
        echo $auth->restorePasswordWithKey($reqData);
        break;
    case "chg":
        unset($reqData['type']);
        echo $auth->changePassword($reqData);
        break;
}