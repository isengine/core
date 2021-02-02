<?php

// Рабочее пространство имен

namespace is;

use is\Model\Constants\Config;
use is\Model\Data\LocalData;
use is\Parents\Path;
use is\Parents\Local;

// Подготавливаем конфигурацию

$local = new Local();
$data = new LocalData($local);
$config = Config::getInstance();

// Читаем настройки системы

$default = [];
require_once __DIR__ . DS . 'default.php';
$data -> setData($default);
unset($default);

// Создаем конфигурацию из констант

$config -> data = $data -> getData();
$config -> initialize();

// Читаем пользовательские настройки

$local -> setFile('configuration.ini');
$data -> joinData($local);

// Читаем настройки для запуска на локальной машине

if ($_SERVER['REMOTE_ADDR'] === $config -> get('system:local')) {
	$local -> setFile('configuration.local.ini');
	$data -> joinData($local);
}

// Создаем конфигурацию из констант

$config -> data = $data -> getData();
$config -> initialize();

// Задаем оставшиеся системные настройки

require_once __DIR__ . DS . 'system.php';

// Делаем проверку системы, но только в режиме разработки

if ($config -> get('default:mode') === 'develop') {
	//require_once __DIR__ . DS . 'check.php';
}

// ТОЛЬКО ДЛЯ ОТЛАДКИ !!!
// Смотрим итоги

$get = $config -> get();

//echo '<pre>' . print_r($config, 1) . '</pre>';
echo '<pre>' . print_r($get, 1) . '</pre>';

?>