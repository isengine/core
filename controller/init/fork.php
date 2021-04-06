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

$path = __DIR__ . DS . DP;

// создаем развилку

if ($state -> get('api')) {
	
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