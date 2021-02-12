<?php

// Рабочее пространство имен

namespace is;

use is\Model\Components\Config;

// задаем конфигурацию php

$config = Config::getInstance();

// принудительно устанавливаем имя идентификатора сессии
// т.к. например в nginx он отказывается его принимать в конфиге

ini_set('session.name', $config -> get('system:session'));

// вывод ошибок рекомендуется включать только на время разработки

if ($config -> get('default:mode') === 'develop') {
	ini_set('display_errors', 'on');
	ini_set('display_startup_errors', true);
	if ($config -> get('log:mode') === 'panic') {
		ini_set('error_reporting', E_ALL);
	} else {
		ini_set('error_reporting', E_ALL & ~E_NOTICE);
	}
} else {
	ini_set('display_errors', 'off');
	ini_set('display_startup_errors', false);
	ini_set('error_reporting', 0);
}

// дополнительные установки локали

$charset = $config -> get('system:charset');

ini_set('default_charset', $charset);

if (version_compare(PHP_VERSION, '5.6.0', '<') && function_exists('mb_internal_encoding')) {
	mb_internal_encoding($charset);
}
if (function_exists('mb_regex_encoding')) {
	mb_regex_encoding($charset);
}

unset($charset);

// установка часового пояса

$timezone = $config -> get('default:timezone');

if ($timezone) {
	date_default_timezone_set($timezone);
}

unset($timezone);

?>