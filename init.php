<?php

namespace is;

// Базовые константы

if (!defined('ISENGINE')) {
    define('ISENGINE', microtime(true));
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('DP')) {
    define('DP', '..' . DIRECTORY_SEPARATOR);
}
if (!defined('DI')) {
    define('DI', realpath($_SERVER['DOCUMENT_ROOT']) . DS);
}
if (!defined('DR')) {
    define('DR', realpath(__DIR__ . DS . DP . DP . DP) . DS);
}

// Подключаем загрузку фреймворка, расширений ядра и классов, вручную прописанных в конфигурации

require_once 'controller' . DS . 'autoload.php';

// Подключаем контроллер ядра

require_once 'controller' . DS . 'controller.php';

exit;
