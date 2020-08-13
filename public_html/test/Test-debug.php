<?php
require_once '../vendor/autoload.php';


$arr1 = ['key1'=> '', 'key2' => ''];
$arr2 = ['key1', 'key2'];

print_r(\RCSE\Core\Utils::compareKeyToValue($arr1,$arr2));