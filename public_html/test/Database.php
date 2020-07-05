<?php
require_once '../vendor/autoload.php';


$data = new \RCSE\Core\Database();


echo "<br>Testing function, result: ". print_r($data->buildQuery_Update('test',['assigments'=>['row'=>1],'condition'=>['row'=>'1', 'r'=>1]]));
echo "<br>Func time: ". xdebug_time_index();