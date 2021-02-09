<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\Strings;
use is\Helpers\System;
use is\Helpers\Sessions;
use is\Model\Constants\Config;
use is\Model\Constants\Session;
use is\Model\Data\LocalData;
use is\Parents\Path;
use is\Parents\Local;

// задаем конфигурацию php

$config = Config::getInstance();
$mode = $config -> get('default:mode');
$errors = null;

// Проверяем версию php

if (version_compare(PHP_VERSION, $config -> get('system:php'), '<')) {
	$errors[] = 'php is not compatible version';
}

// Проверяем существование модулей php
// Также рекомендуется наличие модулей fileinfo, gd | imagick, PDO | mysqli | sqlite3
// Можно вывести все доступные модули:
//echo '<pre>' . print_r(get_loaded_extensions(), 1) . '</pre>';

$extensions = ['curl', 'date', 'intl', 'json', 'mbstring', 'pcre', 'SimpleXML', 'session', 'zip'];

foreach ($extensions as $item) {
	if (!extension_loaded($item)) {
		$errors[] = 'not installed required php extension \"' . $item . '\"';
	}
}

// Проверяем взаимодействие констант

if (
	!$config -> get('default:users') && (
		$config -> get('secure:rights') ||
		$config -> get('secure:csrf') ||
		$config -> get('users:rights') ||
		$config -> get('secure:users')
	) ||
	$config -> get('secure:writing') && (
		!$config -> get('db:writing:user') ||
		!$config -> get('db:writing:pass')
	)
) {
	$errors[] = 'system constants is set wrong';
}

// Проверяем существование системных папок

$folders = ['assets', 'cache', 'custom', 'database', 'extensions', 'log', 'templates'];

foreach ($folders as $item) {
	$path = $config -> get('path:' . $item);
	if (!file_exists($path) || !is_dir($path)) {
		mkdir($path);
		$errors[] = 'system folder from path constant \"' . $item . '\" does not exist and was created';
	}
}

if ($errors) {
	echo '<pre>' . print_r($errors, 1) . '</pre>';
	exit;
}

unset($item, $extensions, $folders);

?>