<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Local;
use is\Components\Config;

// Подготавливаем конфигурацию

$config = Config::getInstance();
$folder = DR . 'config' . DS;
$path = __DIR__ . DS . DP;

// Читаем настройки системы по-умолчанию

$data = System::includes('config:default', $path, true, null, 'default');
$paths = System::includes('config:path', $path, true, null, 'default');
$data['path'] = Objects::merge($data['path'], $paths);
$config->setData($data);
unset($data, $paths);

// Читаем пользовательские настройки

$file = $folder . 'configuration.ini';
$data = Parser::fromJson(Local::readFile($file));
$config->mergeData($data, true);
unset($file, $data);

// Читаем настройки для запуска на локальной машине

if (System::server('ip') === $config->getData('system')['local']) {
    $file = $folder . 'configuration.local.ini';
    $data = Parser::fromJson(Local::readFile($file));
    $config->mergeData($data, true);
    unset($file, $data);
}

// Создаем конфигурацию из констант

$config->init();

// Задаем оставшиеся системные настройки

System::includes('config:system', $path);

// Делаем проверку системы, но только в режиме разработки

if ($config->get('develop:enable')) {
    System::includes('config:check', $path);
}

// Задаем дату и время

System::includes('config:datetime', $path);

// ТОЛЬКО ДЛЯ ОТЛАДКИ !!!
// Смотрим итоги

//$get = $config->get();
//echo '<pre>' . print_r($get, 1) . '</pre>';
