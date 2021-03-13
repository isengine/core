<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\System;
use is\Helpers\Sessions;
use is\Model\Components\Config;
use is\Model\Components\Log;
use is\Model\Components\Display;
use is\Model\Components\Session;
use is\Model\Components\Path;
use is\Model\Components\Local;

// задаем конфигурацию php

$config = Config::getInstance();
$log = Log::getInstance();

// Проверяем версию php

if (version_compare(PHP_VERSION, $config -> get('system:php'), '<')) {
	$log -> data[] = 'php is not compatible [>=' . $config -> get('system:php') . '] version';
}

// Проверяем существование модулей php
// Также рекомендуется наличие модулей 'intl', fileinfo, gd | imagick, PDO | mysqli | sqlite3
// Можно вывести все доступные модули:
//echo '<pre>' . print_r(get_loaded_extensions(), 1) . '</pre>';

$extensions = ['curl', 'date', 'json', 'mbstring', 'pcre', 'SimpleXML', 'session', 'zip'];

foreach ($extensions as $item) {
	if (!extension_loaded($item)) {
		$log -> data[] = 'required php extension [' . $item . '] not installed';
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
	$log -> data[] = 'system constants is set wrong';
}

// Проверяем существование системных папок

$folders = ['app', 'cache', 'extensions', 'log', 'templates'];

foreach ($folders as $item) {
	$path = $config -> get('path:' . $item);
	if (!file_exists($path) || !is_dir($path)) {
		mkdir($path);
		$log -> data[] = 'system folder [' . $path . '] does not exist and was created';
	}
}

if ($log -> data) {
	
	$log -> init();
	$log -> setPath('log');
	
	$print = Display::getInstance();
	$print -> splitter = '<br>';
	$print -> print($log -> data);
	
	$log -> summary();
	$log -> close();
	
	exit;
	
}

unset($item, $extensions, $folders);

?>