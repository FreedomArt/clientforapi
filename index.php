<?php

declare(strict_types=1);
// ------------------------------
$start = microtime(true);
$memory = memory_get_usage();
// ------------------------------

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');

require __DIR__ . '/vendor/autoload.php';

use ClientApi;

$cApi = new \ClientApi\ClientApi(new \GuzzleHttp\Client());

var_dump($cApi);




// -----------------------------------------------
$memory = memory_get_usage() - $memory;
$name = array('байт', 'КБ', 'МБ');
$i = 0;
while (floor($memory / 1024) > 0) {
    $i++;
    $memory /= 1024;
}

$s = round(microtime(true) - $start, 4);
echo '<br/><br/>Время выполнения скрипта: '.$s.' сек. ('.round(($s/60), 6).' мин.)<br/>';
echo 'Скушано памяти: ' . round($memory, 2) . ' ' . $name[$i];
// -----------------------------------------------