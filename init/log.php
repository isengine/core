<?php

// Рабочее пространство имен

namespace is;

use is\Model\Components\Log;

// инициализация

$log = Log::getInstance();
$log -> init();
$log -> setPath('log');

//$log = Log::getInstance();
//$log -> data[] = ...;
//$log -> summary();
//$log -> close(); 

?>