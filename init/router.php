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

//$path -> include('uri:error');

// правила роутинга

if (
	!$state -> get('error') &&
	!$state -> get('api')
) {
	
	// сравниваем урлы и разрешаем релоад
	// только если не была установлена ошибка
	// и только если релоад задан в настройках
	
	if (
		$uri -> url !== $uri -> original &&
		$config -> get('router:reload')
	) {
		Sessions::reload($uri -> url, 301);
	}
	
}

// предыдущая страница через куки

$path -> include('router:previous');

// устанавливаем заголовок

Sessions::setHeaderCode($state -> get('error') ? $state -> get('error') : 200);

echo '<pre>';
//echo print_r($a, 1) . '<br>';
//echo print_r($api, 1);
echo print_r($uri, 1);
echo '</pre>';

//$print = Display::getInstance();
//$print -> dump($uri);
//
//exit;

?>