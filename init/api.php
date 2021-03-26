<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Helpers\Match;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Apis\Api;

// инициализация

$config = Config::getInstance();
$uri = Uri::getInstance();
$state = State::getInstance();

$api_name = $config -> get('url:api:name');

if ($api_name && $uri -> getPathArray(0) === $api_name) {
	
	$state -> set('api', true);
	
	// загружаем последовательность инициализации
	
	$path = __DIR__ . DS . DP;
	
	System::includes('api:base', $path);
	System::includes('api:settings', $path);
	System::includes('api:data', $path);
	
} else {
	$state -> set('api', false);
}

unset($api_name);

?>