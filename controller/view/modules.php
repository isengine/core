<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Paths;
use is\Components\Config;
use is\Components\State;
use is\Components\Display;
use is\Components\Log;
use is\Components\Router;
use is\Components\Language;
use is\Masters\View;
use is\Masters\Module;

// читаем конфиг

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();
$view = View::getInstance();
$module = Module::getInstance();

// запускаем поддержку модулей

$module -> init(
	$config -> get('path:vendors'),
	$config -> get('path:app') . 'Masters' . DS . 'Modules' . DS,
	$config -> get('path:cache') . 'modules' . DS,
	$config -> get('cache:modules')
);

$view -> set('module', $module);

?>