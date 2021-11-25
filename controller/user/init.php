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

// проверяем, разрешены ли пользователи в системе
if (!$config -> get('users:enable')) {
	return;
}

// загружаем последовательность инициализации
$path = __DIR__;

// если сессия была открыта и доступ был разрешен
if (
	$state -> get('session') ||
	$state -> get('api') && $state -> get('api') !== true
) {
	
	// раньше по условиям проверки было достаточно проверки
	// на любое положительное значение apikey
	// но теперь (10-11-21) мы изменили условия
	// есть ключ апи не был передан, т.е. равен true, а не строке
	// инициализация пользователя не происходит
	// а зачем? если пользователь уже был авторизован,
	// то система сюда зайдет сама, т.к. будет открыта сессия
	// без этого изменения нельзя было обработать простой
	// общедоступный запрос по api вроде отправки формы
	echo '**';
	// читаем настройки полей пользователя
	System::includes('settings', $path);
	
	// инициализация пользователя со всеми данными
	System::includes('data', $path);
	
	if ($config -> get('users:secure')) {
		// более глубокая проверка пользователя по базе данных
		// инициализация пользователя по базе данных - смотрим привязки к браузерам и ip
		System::includes('secure', $path);
	}
	
}

// читаем права пользователя
System::includes('rights', $path);

//$print = Display::getInstance();
//$print -> dump($user -> getData());
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($state);
//
//exit;

?>