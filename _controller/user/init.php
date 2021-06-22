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
use is\Components\User;

// читаем user

$config = Config::getInstance();
$state = State::getInstance();
$user = User::getInstance();

$user -> init();

// загружаем последовательность инициализации
$path = __DIR__;

// если сессия была открыта и доступ был разрешен

if (
	$state -> get('session') ||
	$state -> get('api')
) {
	
	// читаем настройки полей пользователя
	System::includes('settings', $path);
	
	// инициализация пользователя со всеми данными
	System::includes('data', $path);
	
	if ($config -> get('secure:users')) {
		// более глубокая проверка пользователя по базе данных
		// инициализация пользователя по базе данных - смотрим привязки к браузерам и ip
		System::includes('allow', $path);
	}
	
}

// читаем права пользователя
if ($config -> get('secure:rights')) {
	System::includes('rights', $path);
}

//$print = Display::getInstance();
//$print -> dump($user -> getData());
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($state);
//
//exit;

?>