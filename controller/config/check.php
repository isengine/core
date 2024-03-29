<?php

namespace is;

use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\System;
use is\Helpers\Sessions;
use is\Components\Config;
use is\Components\Log;
use is\Components\Display;
use is\Components\Session;

// задаем конфигурацию php

$config = Config::getInstance();
$log = Log::getInstance();

// Проверяем версию php

if (version_compare(PHP_VERSION, $config->get('system:php'), '<')) {
    $log->data[] = 'php is not compatible [>=' . $config->get('system:php') . '] version';
}

// Проверяем существование модулей php
// Также рекомендуется наличие модулей 'intl', fileinfo, gd | imagick, PDO | mysqli | sqlite3
// Можно вывести все доступные модули:
//echo '<pre>' . print_r(get_loaded_extensions(), 1) . '</pre>';

$extensions = ['curl', 'date', 'json', 'mbstring', 'pcre', 'SimpleXML', 'session', 'zip'];

foreach ($extensions as $item) {
    if (!extension_loaded($item)) {
        $log->data[] = 'required php extension [' . $item . '] not installed';
    }
}

// Проверяем взаимодействие констант

if (
    $config->get('secure:writing')
    && (
        !$config->get('db:writing:user')
        || !$config->get('db:writing:pass')
    )
) {
    $log->data[] = 'system constants is set wrong';
}

// Проверяем существование системных папок

$folders = ['app', 'cache', 'vendors', 'log', 'templates'];

foreach ($folders as $item) {
    $path = $config->get('path:' . $item);
    if (!file_exists($path) || !is_dir($path)) {
        mkdir($path);
        $log->data[] = 'system folder [' . $path . '] does not exist and was created';
    }
}

if ($log->data) {
    $log->init();
    $log->setPath('log');

    $print = Display::getInstance();
    $print->splitter = '<br>';
    $print->render($log->data);

    $log->summary();
    $log->close();

    exit;
}

unset($item, $extensions, $folders);
