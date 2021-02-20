<?php

// Рабочее пространство имен

namespace is;

// Базовые константы

if (!defined('isENGINE')) { define('isENGINE', microtime(true)); }
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
if (!defined('DP')) { define('DP', '..' . DIRECTORY_SEPARATOR); }
if (!defined('DR')) { define('DR', realpath(__DIR__ . DS . DP . DP . DP) . DS); }

// Include framework
// Подключаем фреймворк

$autoload = DR . 'vendor' . DS . 'autoload.php';
$framework = DR . 'vendor' . DS . 'isengine' . DS . 'framework' . DS . 'php' . DS . 'init.php';
require_once file_exists($autoload) ? $autoload : $framework;
unset($autoload, $framework);

// Pre-check for blocking by ip
// Предварительная проверка на блокировку по ip

require_once 'init' . DS . 'preload.php';

// Create including components
// Создаем подключение компонентов

$path = new Model\Components\Path(__DIR__);

// Launch system configuration
// Запускаем конфигурацию системы

$path -> include('init:config');

// Launch error page settings
// Запускаем настройку страницы ошибок

$path -> include('init:error');

// Launch logs
// Запускаем логи

$path -> include('init:log');

// Launch session set and check
// Запускаем установку и проверку сессии

$path -> include('init:session');

// Launch check query
// Запускаем проверку качества запроса

$path -> include('init:request');

// Launch check cookie
// Запускаем проверку работы куки

$path -> include('init:cookie');

// Launch uri
// Запускаем разбор uri

$path -> include('init:uri');

// Launch driver db
// Запускаем инициализацию драйвера базы данных

$path -> include('init:driver');

// Launch user
// Запускаем инициализацию пользователя

//$path -> include('init:user');

//старый порядок
//init('users', 'first');
//init('drivers', 'first');
//init('users', 'second');
//init('core', 'languages');
//init('processor', 'first');
//init('core', 'structure');
//init('templates', 'first');
//init('templates', 'router');
//init('templates', 'second');

// Launch display buffer
// Запускаем буфер вывода на экран

$path -> include('init:display');




echo '<hr><p>END OF TESTS<br>' . number_format(memory_get_usage() / 1024, 3, '.', ' ') . ' KB total / ' . number_format(memory_get_peak_usage() / 1024, 3, '.', ' ') . ' KB in peak<br>' . number_format(microtime(true) - isENGINE, 3, null, null) . ' sec is speed</p>';

?>