<?php

namespace is;

use is\Components\Config;
use is\Components\Log;

// инициализация

$config = Config::getInstance();
$path = $config->get('path:log');

$log = Log::getInstance();
$log->init();
$log->setPath($path);

//$log = Log::getInstance();
//$log->data[] = ...;
//$log->summary();
//$log->close();
