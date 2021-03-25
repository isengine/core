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

require_once 'init' . DS . 'blockip.php';

// Create including components
// Создаем подключение компонентов

$path = new Model\Components\Path(__DIR__);

// Launch system configuration
// Запускаем конфигурацию системы

$path -> include('init:config');

// Launch logs
// Запускаем логи

$path -> include('init:log');

// Launch error
// Запускаем компонент ошибки

$path -> include('init:error');

// Launch session set and check
// Запускаем установку и проверку сессии

$path -> include('init:session');

// Launch check query
// Запускаем проверку качества запроса

$path -> include('init:request');

// Launch check cookie
// Запускаем проверку работы куки

$path -> include('init:cookie');

// если есть ошибка, нет смысла что-либо разбирать
//$state = Model\Components\State::getInstance();
//if (!$state -> get('error')) {
//}

// Launch uri
// Запускаем разбор uri

$path -> include('init:uri');

// Launch driver db
// Запускаем инициализацию драйвера базы данных

$path -> include('init:driver');

// Launch api
// Запускаем api

$path -> include('init:api');

// Launch user
// Запускаем инициализацию пользователя

$path -> include('init:user');

// Launch language initialization
// Запускаем инициализацию языков

$path -> include('init:language');
// возможно, здесь не хватает инициализации языкового модуля и библиотек

// Launch routing
// Запускаем правила роутинга

$path -> include('init:router');

// Launch display buffer
// Запускаем буфер вывода на экран

$path -> include('init:display');

// Include view
// Подключаем вид, шаблонизатор

$path -> include('init:view');

// test set

echo '<!--noindex--><hr><p>END OF TESTS<br>' . number_format(memory_get_usage() / 1024, 3, '.', ' ') . ' KB total / ' . number_format(memory_get_peak_usage() / 1024, 3, '.', ' ') . ' KB in peak<br>' . number_format(microtime(true) - isENGINE, 3, null, null) . ' sec is speed</p><!--/noindex-->';

exit;

?>