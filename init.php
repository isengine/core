<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;

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

$path = __DIR__;

// Launch system configuration
// Запускаем конфигурацию системы

System::include('init:config', $path);

// Launch logs
// Запускаем логи

System::include('init:log', $path);

// Launch error
// Запускаем компонент ошибки

System::include('init:error', $path);

// Launch session set and check
// Запускаем установку и проверку сессии

System::include('init:session', $path);

// Launch check query
// Запускаем проверку качества запроса

System::include('init:request', $path);

// Launch check cookie
// Запускаем проверку работы куки

System::include('init:cookie', $path);

// если есть ошибка, нет смысла что-либо разбирать
//$state = Model\Components\State::getInstance();
//if (!$state -> get('error')) {
//}

// Launch uri
// Запускаем разбор uri

System::include('init:uri', $path);

// Launch driver db
// Запускаем инициализацию драйвера базы данных

System::include('init:driver', $path);

// Launch api
// Запускаем api

System::include('init:api', $path);

// Launch user
// Запускаем инициализацию пользователя

System::include('init:user', $path);

// Launch language initialization
// Запускаем инициализацию языков

System::include('init:language', $path);

// возможно, здесь не хватает инициализации языкового модуля и библиотек

// Launch routing
// Запускаем правила роутинга

System::include('init:router', $path);

// Launch display buffer
// Запускаем буфер вывода на экран

System::include('init:display', $path);

// Include view
// Подключаем вид, шаблонизатор

System::include('init:view', $path);

// test set

echo '<!--noindex--><hr><p>END OF TESTS<br>' . number_format(memory_get_usage() / 1024, 3, '.', ' ') . ' KB total / ' . number_format(memory_get_peak_usage() / 1024, 3, '.', ' ') . ' KB in peak<br>' . number_format(microtime(true) - isENGINE, 3, null, null) . ' sec is speed</p><!--/noindex-->';

exit;

?>