<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\Sessions;
use is\Model\Components\State;

// читаем сессию

$state = State::getInstance();

if (Sessions::getCookie('isENGINE')) {
	$state -> set('cookie', true);
} else {
	
	// работа следующего алгоритма более надежна,
	// но требует больше системных ресурсов
	
	session_start();
	$a = session_id();
	session_destroy();
	
	session_start();
	$b = session_id();
	session_destroy();
	
	$state -> set('cookie', $a === $b);
	
	unset($a, $b);
	
	// работа алгоритма ниже достаточно относительна,
	// т.к. он будет корректно работать только при переинициализации страницы
	// но он позволяет не делать многократной переинициализации сессии
	
	$time = new \DateTime();
	Sessions::setCookie('isENGINE', $time -> getTimestamp());
	
}

?>