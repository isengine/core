<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

// загружаем последовательность инициализации

$path = __DIR__;

// вызов метода апи

// Запускаем разбор структуры сайта
System::includes('structure', $path);

// Запускаем базовый роутинг
System::includes('base', $path);

// правила роутинга

if (
	!$state -> get('error') &&
	$config -> get('router:reload')
) {
	System::includes('reload', $path);
}

// определяем шаблон

System::includes('template', $path);

// предыдущая страница через куки

System::includes('previous', $path);

// устанавливаем заголовок

System::includes('headers', $path);

//$print = Display::getInstance();
//$print -> dump($uri);
//
//exit;

?>