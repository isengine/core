<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Helpers\Match;
use is\Model\Components\Path;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Uri;
use is\Model\Apis\Api;

// инициализация

$config = Config::getInstance();
$uri = Uri::getInstance();

$api_name = $config -> get('url:api:name');

if ($api_name && $uri -> getPathArray(0) === $api_name) {
	
	// загружаем последовательность инициализации
	
	$path = new Path(__DIR__ . DS . DP);
	
	$path -> include('api:base');
	$path -> include('api:settings');
	$path -> include('api:data');
	
}

unset($api_name);

?>