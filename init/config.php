<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Local;
use is\Model\Components\Config;

// Подготавливаем конфигурацию

$config = Config::getInstance();
$folder = DR . 'config' . DS;
$path = __DIR__ . DS . DP;

// Читаем настройки системы по-умолчанию

$data = System::include('config:default', $path, 'default');
$config -> setData($data);
unset($data);

// Читаем пользовательские настройки

$file = $folder . 'configuration.ini';
$data = Parser::fromJson( Local::readFile($file) );
$config -> mergeData($data, true);
unset($file, $data);

// Читаем настройки для запуска на локальной машине

if (System::server('ip') === $config -> getData('system')['local']) {
	$file = $folder . 'configuration.local.ini';
	$data = Parser::fromJson( Local::readFile($file) );
	$config -> mergeData($data, true);
	unset($file, $data);
}

// Создаем конфигурацию из констант

$config -> init();

// Задаем оставшиеся системные настройки

System::include('config:system', $path);

// Делаем проверку системы, но только в режиме разработки

if ($config -> get('default:mode') === 'develop') {
	System::include('config:check', $path);
}

// ТОЛЬКО ДЛЯ ОТЛАДКИ !!!
// Смотрим итоги

//$get = $config -> get();
//echo '<pre>' . print_r($get, 1) . '</pre>';

?>