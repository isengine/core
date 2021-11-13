<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

// загружаем последовательность инициализации

$path = __DIR__ . DS . DP;

// создаем развилку

if ($config -> get('develop:test') && isset($_GET['test'])) {
	
	// Запускаем тестирование, но только в режиме тестирования
	System::includes('test:init', $path . DP . DP . 'framework');
	
} elseif ($state -> get('api')) {
//} elseif ($state -> get('api') && $state -> get('api') !== true) {
	// а вот эта проверка неправильная, т.к.
	// переход на апи должен происходить в любом случае
	
	// Запускаем api
	System::includes('api:init', $path);
	
} else {
	
	// Launch routing
	// Запускаем правила роутинга
	
	System::includes('router:init', $path);
	
	// Include view
	// Подключаем вид, шаблонизатор
	
	System::includes('view:init', $path);
	
}

?>