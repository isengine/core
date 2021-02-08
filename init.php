<?php

// Рабочее пространство имен

namespace is;

// Базовые константы

if (!defined('isENGINE')) { define('isENGINE', microtime(true)); }
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
if (!defined('DP')) { define('DP', '..' . DIRECTORY_SEPARATOR); }
if (!defined('DR')) { define('DR', realpath(__DIR__ . DS . DP . DP . DP) . DS); }

define('isCORE', realpath(__DIR__) . DS);

echo 'isCORE : ' . isCORE . '<br>';

// Переменные путей

//$autoload =  . DS . 'vendor' . DS . 'autoload.php';
//$isengine . 'framework' . DS . 'php' . DS . 'init.php';


// Include framework
// Подключаем фреймворк

$framework = $isengine . 'framework' . DS . 'php' . DS . 'init.php';
require_once file_exists($autoload) ? $autoload : $framework;
unset($autoload, $isengine, $framework);

// Launch system configuration
// Запускаем конфигурацию системы

require_once isCORE . 'init' . DS . 'config.php';

// Launch session set and check
// Запускаем установку и проверку сессии

require_once isCORE . 'init' . DS . 'session.php';

?>