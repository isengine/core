<?php

// ISENGINE - метка системы с таймингом запуска

if (!defined('ISENGINE')) {
    define('ISENGINE', microtime(true));
}

// DS, DIRECTORY SEPARATOR - разделитель папок

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// DP, DIRECTORY PARENT - предыдущая папка

if (!defined('DP')) {
    define('DP', '..' . DIRECTORY_SEPARATOR);
}

// DI, DIRECTORY INDEX - индексная, публичная папка проекта

if (!defined('DI')) {
    define('DI', realpath($_SERVER['DOCUMENT_ROOT']) . DS);
}

// DR, DIRECTORY ROOT - корневая папка проекта

if (!defined('DR')) {
    define('DR', realpath(__DIR__ . DS . DP . DP . DP) . DS);
}
