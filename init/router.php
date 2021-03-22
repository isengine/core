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

// загружаем последовательность инициализации

$path = new Path(__DIR__ . DS . DP);

// вызов метода апи

if ($state -> get('api')) {
	$path -> include('router:api');
} else {
	// Запускаем разбор структуры сайта
	$path -> include('router:structure');
	// Запускаем базовый роутинг
	$path -> include('router:base');
}

// правила роутинга

if (
	!$state -> get('error') &&
	$config -> get('router:reload')
) {
	$path -> include('router:reload');
}

// определяем шаблон

$path -> include('router:template');

// предыдущая страница через куки

$path -> include('router:previous');

// устанавливаем заголовок

$path -> include('router:headers');

//$print = Display::getInstance();
//$print -> dump($uri);
//
//exit;

?>