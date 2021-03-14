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

// читаем сессию

if (
	Sessions::getCookie('SID', true) ||
	Sessions::getCookie('UID', true)
) {
	
	session_start();
	$state -> set('session', true);
	
} else {
	
	// объявить константы здесь - обязательно,
	// так как это защищает от возможности изменить их в дальнейшем коде
	$state -> set('session', false);
	
}

$session = Session::getInstance();
$session -> init();

// проверяем сессию

if ($state -> get('session')) {
	
	$config = Config::getInstance();
	$session_time = $config -> get('secure:sessiontime');
	
	if (
		Sessions::getCookie('SID') !== $session -> getSession('id') ||
		Sessions::getCookie('UID') !== $session -> getSession('uid') ||
		$state -> get('origin')
	) {
		
		$session -> reset();
		
		$state -> set('error', 403);
		$state -> set('reason', 'bad SID or UID or not is origin, see php session configuration and maybe session or cookies were stolen');
		
	} elseif ($session_time) {
		
		$time = time();
		
		if (
			$time > Prepare::decrypt($session -> token, true) + (int) $session_time ||
			$time < Prepare::decrypt($session -> token, true)
		) {
			
			// удаляем пользователя из базы данных / удаляем файл и записываем нового
			// но для этого нужно подключить функцию записи/удаления из базы
			// еще будет идти проверка на существование пользователя (по id/name) в базе данных,
			// и если такого нет, любой запрос будет отвергнут
			
			if (!$_SESSION['secure']) {
				
				$session -> reset();
				
				$state -> set('error', 403);
				$state -> set('reason', 'wrong session when it was regenerate, maybe session or cookies were stolen');
				
			}
			
			$session -> reinit();
			
			Sessions::setCookie('SID', session_id());
			Sessions::setCookie('UID', $session -> uid);
			
		}
		
		unset($time);
		
	}
	
}


?>