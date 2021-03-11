<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Url;
use is\Helpers\Prepare;
use is\Helpers\Ip;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Controller\Database;

// читаем user

$user = User::getInstance();
$state = State::getInstance();
$session = Session::getInstance();

// обработчик ошибок при проверке

// пока здесь только отчеты в логах, ну кроме ошибки за бан
// нужно сделать вывод или отправку уведомлений пользователю
// а также механизм добавления новых записей в базу данных
// и подтверждения у пользователя на их добавление с уведомлением по e-mail,
// вводом капчи - ну в общем как положено
// возможно, это придется сделать через шаблон ошибки или через шаблон восстановления доступа

if ($user -> getFieldsBySpecial('allow')) {
	
	// проверка на бан
	
	$session -> reset();
	
	$error = Error::getInstance();
	$error -> code = 403;
	$error -> reason = 'security user verification - user are banned';
	$error -> reload();
	
} else {
	
	$allow = [
		'ip' => null,
		'agent' => null,
		'user_ip' => $user -> getFieldsBySpecial('allowip'),
		'user_agent' => $user -> getFieldsBySpecial('allowagent'),
		'session_ip' => $session -> getSession('ip'),
		'session_agent' => md5($session -> getSession('agent')),
	];
	
	// проверка на присутствие текущего ip в списке разрешенных
	
	if (
		$allow['user_ip'] &&
		Ip::range($allow['session_ip'], $allow['user_ip'])
	) {
		$allow['ip'] = true;
	}
	
	// проверка на присутствие текущего хэша агента в списке разрешенных
	
	if (
		$allow['user_agent'] &&
		in_array($allow['session_agent'], $allow['user_agent'])
	) {
		$allow['agent'] = true;
	}
	
	if ($allow['ip'] && !$allow['agent']) {
		
		//logging('security user verification - unknown agent but known ip, agent will be added in list');
		
		$user -> addFieldsBySpecial('allowagent', $allow['session_agent']);
		
		// сюда не хватает записи о перезаписи $user -> allow['allowagent'] в базу данных
		// он уже массив, так что никаких дополнительных условий делать не нужно
		// разве только узнать имя поля в базе данных пользователя
		
	} elseif (!$allow['ip'] && $allow['agent']) {
		
		//logging('security user verification - unknown ip but known agent, ip will be added in list with extended diapason');
		
		$user -> addFieldsBySpecial('allowip', $allow['session_ip']);
		
		// сюда не хватает записи о перезаписи $user -> allow['allowip'] в базу данных
		// он уже массив, так что никаких дополнительных условий делать не нужно
		// разве только узнать имя поля в базе данных пользователя
		
	} elseif (!$allow['ip'] && !$allow['agent']) {
		
		//logging('security user verification - unknown ip and agent, user must be notified and added this in lists');
		
	}
	
	unset($allow);
	
}

?>