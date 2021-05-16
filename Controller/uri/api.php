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

// инициализация

$config = Config::getInstance();
$uri = Uri::getInstance();
$state = State::getInstance();

$api_name = $config -> get('api:name');
$api_key = $config -> get('api:key');

if (
	$config -> get('api:server') ||
	$api_name && (
		$uri -> getPathArray(0) === $api_name ||
		$uri -> getPathArray(1) === $api_name
	)
) {
	$key = $uri -> getData($api_key);
	$state -> set('api', $key ? $key : true);
	unset($key);
} else {
	$state -> set('api', false);
}

$uri -> deleteDataKey($api_key);

unset($api_name, $api_key);

?>