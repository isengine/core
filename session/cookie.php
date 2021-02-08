<?php defined('isENGINE') or die;

use is\Functions\System;

// работа данной константы достаточно относительна,
// т.к. она будет корректно работать только при переинициализации страницы

if (System::cookie('isENGINE', true)) {
	define('isCOOKIE', true);
} else {
	System::cookie('isENGINE', 'enabled');
	define('isCOOKIE', false);
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