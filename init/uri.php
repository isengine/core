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

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();

$uri = Uri::getInstance();
$uri -> init();
$uri -> setInit();

// загружаем последовательность инициализации

$path = new Path(__DIR__ . DS . DP);

// set error and api
$path -> include('uri:error');

// если есть ошибка, нет смысла что-либо разбирать

if (!$state -> get('error')) {
	
	$path -> include('uri:api');
	$path -> include('uri:base');
	$path -> include('uri:path');
	
	// сравниваем урлы и разрешаем релоад
	// только если не была установлена ошибка
	// только если задан в настройках
	
	if (
		$uri -> url !== $uri -> original &&
		$config -> get('router:reload')
	) {
		Sessions::reload($uri -> url, 301);
	}
	
}

// предыдущая страница через куки

$path -> include('uri:previous');

// устанавливаем заголовок

if ($state -> get('error')) {
	Sessions::setHeaderCode($state -> get('error'));
} else {
	Sessions::setHeaderCode(200);
}

echo print_r($_SERVER, 1) . '<br>';
echo print_r($state, 1) . '<br>';

$print = Display::getInstance();
$print -> dump($uri);

exit;

?>