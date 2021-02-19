<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Url;
use is\Helpers\Prepare;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Api;

// инициализация

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

$api = Api::getInstance();
$api -> init($config -> get('api:url'));
$api -> prefix = $config -> get('api:prefix');
$api -> postfix = $config -> get('api:postfix');

// читаем uri

$apipath = $api -> path . $api -> prefix;

// api

if (
	$apipath &&
	(
		Strings::match('/' . $uri -> path['string'], $apipath) ||
		Strings::match('/' . $uri -> query['string'], $apipath)
	)
) {
	$state -> set('api', true);
}

//use is\Model\Components\Api;
//$api = Api::getInstance();
//$api -> name = 404;
//$api -> reload();

?>