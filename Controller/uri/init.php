<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();

$uri = Uri::getInstance();
$uri -> init();

// загружаем последовательность инициализации

$path = __DIR__;

System::includes('base', $path);
System::includes('path', $path);
System::includes('data', $path);
System::includes('api', $path);

//$print = Display::getInstance();
//$print -> dump($uri);
//
//exit;

?>