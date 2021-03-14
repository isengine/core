<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Api;

// инициализация

$config = Config::getInstance();
$state = State::getInstance();

$api = Api::getInstance();
$api -> init($config -> get('url:api:url'));
$api -> prefix = $config -> get('url:api:prefix');
$api -> postfix = $config -> get('url:api:postfix');

// читаем uri

$path = $api -> path . $api -> prefix;
$find = System::server('request');

// api

if ($path && Strings::match($find, $path)) {
	$state -> set('api', true);
}

unset($path, $find);

//use is\Model\Components\Api;
//$api = Api::getInstance();
//$api -> name = 404;
//$api -> reload();

?>