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
use is\Model\Components\User;
use is\Model\Databases\Database;

// читаем user

$config = Config::getInstance();
$state = State::getInstance();
$user = User::getInstance();

$user -> init();

// если сессия была открыта и доступ был разрешен

if ($state -> get('session')) {
	
	// загружаем последовательность инициализации
	$path = new Path(__DIR__ . DS . DP);
	
	// инициализация пользователя со всеми данными
	$path -> include('user:data');
	
	// читаем настройки полей пользователя
	$path -> include('user:settings');
	
	if ($config -> get('secure:users')) {
		// более глубокая проверка пользователя по базе данных
		// инициализация пользователя по базе данных - смотрим привязки к браузерам и ip
		$path -> include('user:allow');
	}
	
	if ($config -> get('secure:rights')) {
		// читаем права пользователя
		$path -> include('user:rights');
	}
	
}

//echo print_r($_SERVER, 1) . '<br>';
//echo print_r($state, 1) . '<br>';

//$print = Display::getInstance();
//$print -> dump($user);
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($state);
//
//exit;

?>