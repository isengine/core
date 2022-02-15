<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Components\Config;
use is\Components\State;
use is\Components\Display;
use is\Components\Log;
use is\Components\Uri;
use is\Masters\Api;

// инициализация

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

// инициализируем апи с параметрами

$index = Objects::find($uri -> getPathArray(), $config -> get('api:name'));

$apiset = [
	'class' => $uri -> getPathArray($index + 1),
	'method' => $uri -> getPathArray($index + 2),
	'key' => $state -> get('api'),
	'token' => $uri -> getData( $config -> get('api:token') ),
	'data' => $uri -> getData()
];

$api = Api::getInstance();
$api -> init($apiset);

unset($apiset, $index);

//echo print_r($api, 1);

?>