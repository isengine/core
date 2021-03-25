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
use is\Model\Components\User;

// читаем user

$config = Config::getInstance();
$state = State::getInstance();
$user = User::getInstance();

$user -> init();

// загружаем последовательность инициализации
$path = __DIR__ . DS . DP;

// если сессия была открыта и доступ был разрешен

if (
	$state -> get('session') ||
	$state -> get('api')
) {
	
	// читаем настройки полей пользователя
	System::include('user:settings', $path);
	
	// инициализация пользователя со всеми данными
	System::include('user:data', $path);
	
	if ($config -> get('secure:users')) {
		// более глубокая проверка пользователя по базе данных
		// инициализация пользователя по базе данных - смотрим привязки к браузерам и ip
		System::include('user:allow', $path);
	}
	
}

// читаем права пользователя
if ($config -> get('secure:rights')) {
	System::include('user:rights', $path);
}

//$print = Display::getInstance();
//$print -> dump($user -> getData());
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($state);
//
//exit;

?>