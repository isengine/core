<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\Config;
use is\Model\Components\State;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Uri;
use is\Model\Apis\Api;

// инициализация

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

// инициализируем апи с параметрами

$apiset = [
	'class' => $uri -> getPathArray(1),
	'method' => $uri -> getPathArray(2),
	'key' => $uri -> getData( $config -> get('url:api:key') ),
	'token' => $uri -> getData( $config -> get('url:api:token') ),
	'data' => $uri -> getData()
];

unset($apiset['data'][ $config -> get('url:api:key') ]);

$api = Api::getInstance();
$api -> init($apiset);

$state -> set('api', System::set($api -> key));

?>