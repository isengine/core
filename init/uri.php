<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();

$uri = Uri::getInstance();
$uri -> init();
$uri -> setInit();

// загружаем последовательность инициализации

$path = new Path(__DIR__ . DS . DP);

$path -> include('uri:base');
$path -> include('uri:path');

//$print = Display::getInstance();
//$print -> dump($uri);
//
//exit;

?>