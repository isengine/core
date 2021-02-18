<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\Sessions;
use is\Model\Components\State;

// читаем сессию

$state = State::getInstance();

// работа данного алгоритма достаточно относительна,
// т.к. он будет корректно работать только при переинициализации страницы

if (Sessions::getCookie('isENGINE')) {
	$state -> set('cookie', true);
} else {
	$state -> set('cookie', false);
	$time = new \DateTime();
	Sessions::setCookie('isENGINE', $time -> getTimestamp());
}

// работа следующего алгоритма более надежна,
// но требует больше системных ресурсов

/*
session_start();
$a = session_id();
session_destroy();
session_start();
$b = session_id();
session_destroy();
define('isCOOKIE', $a == $b ? true : false);
unset($a, $b);
*/

?>