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

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();

// загружаем последовательность инициализации

$path = __DIR__;

// вызов метода апи

// Запускаем инициализацию генератора файлов
System::includes('files', $path);

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

// загружаем настройки шаблона

System::includes('settings', $path);

// проверка доступа к шаблону

System::includes('secure', $path);

if ($state -> get('blockip')) {
	System::includes('template', $path, null, null);
	System::includes('settings', $path, null, null);
}

// предыдущая страница через куки

System::includes('previous', $path);

// устанавливаем заголовок

System::includes('headers', $path);

//$print = Display::getInstance();
//$print -> dump($uri);
//
//exit;

?>