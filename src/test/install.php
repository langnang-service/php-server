<?php


require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';


$inc = new Langnang\PhpServer\Install\Install(__DIR__ . '/../../config.inc.php');


var_dump($inc);
