<?php
$cars = [
    ['name' => 'Такси 1', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
    ['name' => 'Такси 2', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
    ['name' => 'Такси 3', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
    ['name' => 'Такси 4', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
    ['name' => 'Такси 5', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
];

$passenger = rand(0, 1000);

/* ===== Ваш код ниже ===== */
$ranges = array();
foreach($cars as $taxi) {
    $ranges[] = abs($passenger - $taxi['position']);
}

$temp = array();
for($i = 0; $i < count($ranges); $i++) {
    if($cars[$i]['isFree']) $temp[] = $ranges[$i];
}

$minRng = array_search(min($temp), $ranges, true);

foreach($cars as $key => $taxi) {
    echo "{$taxi['name']}, строит на {$taxi['position']} км, до пассажира {$ranges[$key]} км ";
    if ($taxi['isFree']) echo "(свободен)";
    else echo "(занят)";

    if($key == $minRng && $taxi['isFree']) {
        echo " - едет это такси";
    }

    echo "<br>";
}