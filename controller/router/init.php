<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;

use is\Components\Router;

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

// Запускаем определение rest
System::includes('rest', $path);

// Запускаем проверку типов
System::includes('content', $path);

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
	System::includes('template', $path, null);
	System::includes('settings', $path, null);
}

// предыдущая страница через куки

System::includes('previous', $path);

// устанавливаем заголовок

System::includes('headers', $path);

?>