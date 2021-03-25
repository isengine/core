<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();

$uri = Uri::getInstance();
$uri -> init();

// загружаем последовательность инициализации

$path = __DIR__ . DS . DP;

System::include('uri:base', $path);
System::include('uri:path', $path);
System::include('uri:data', $path);

//$print = Display::getInstance();
//$print -> dump($uri);
//
//exit;

?>