<?php

// Рабочее пространство имен

namespace is;

use is\Model\Components\Config;
use is\Model\Components\Path;
use is\Model\Components\Content;

// Подготавливаем конфигурацию

$data = new Content();
$config = Config::getInstance();

$path = new Path(__DIR__ . DS . DP);

// Читаем настройки системы по-умолчанию

$default = $path -> include('config:default', 'default');
$data -> addContent($default);
unset($default);

// Создаем конфигурацию из констант
//$config -> data = $data -> getData();
//$config -> init();

// Читаем пользовательские настройки

$data -> setFile('configuration.ini');
$data -> readContent();

// Читаем настройки для запуска на локальной машине

if ($_SERVER['REMOTE_ADDR'] === $config -> get('system:local')) {
	$data -> setFile('configuration.local.ini');
	$data -> readContent();
}

// Создаем конфигурацию из констант

$config -> data = $data -> getContent();
$config -> init();

// Задаем оставшиеся системные настройки

$path -> include('config:system');

// Делаем проверку системы, но только в режиме разработки

if ($config -> get('default:mode') === 'develop') {
	$path -> include('config:check');
}

// ТОЛЬКО ДЛЯ ОТЛАДКИ !!!
// Смотрим итоги

//$get = $config -> get();
//echo '<pre>' . print_r($get, 1) . '</pre>';

?>