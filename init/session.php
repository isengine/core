<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Session;
use is\Model\Components\Config;
use is\Model\Components\State;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Error;

// базовые установки

$state = State::getInstance();
$session = Session::getInstance();

// читаем сессию
// объявить константы здесь - обязательно,
// так как это защищает от возможности изменить их в дальнейшем коде

if (Sessions::getCookie('session')) {
	$session -> open();
	$state -> set('session', true);
} else {
	$state -> set('session', false);
}

$session -> init();

// для инициализации пользователя типа
// user -> session() : {
// $session = Session::getInstance();
// $session -> open();
// $session -> setValue('user', json_encode($user));
// Sessions::setCookie('session', $session -> getSession('token'));
// }

// проверяем сессию

if ($state -> get('session')) {
	
	$time = time();
	$config = Config::getInstance();
	$session_time = (int) $config -> get('secure:sessiontime');
	
	$token = $session -> getSession('token');
	$token_array = json_decode(Prepare::decode($token), true);
	$token_time = $token_array['time'];
	$token_verify = true;
	
	foreach (['id', 'ip', 'agent'] as $item) {
		if (!$token_array[$item] || $token_array[$item] !== $session -> getSession($item)) {
			$token_verify = null;
			break;
		}
	}
	unset($item);
	
	if (
		!$state -> get('request') ||
		Sessions::getCookie('session') !== $token ||
		$token_time > $time ||
		!$token_verify
	) {
		
		$session -> close();
		
		$state -> set('error', 403);
		$state -> set('reason', 'bad session token or not is origin, see php session configuration and maybe session or cookies were stolen');
		
	} elseif ($session_time && ($token_time < $time - $session_time)) {
		
		// если время токена слишком устарело
		// обновляем сессию
		
		$session -> reinit();
		
	}
	
}

?>