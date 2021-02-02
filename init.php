<?php

// Рабочее пространство имен

namespace is;

// Базовые константы

if (!defined('isENGINE')) { define('isENGINE', microtime(true)); }
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
if (!defined('DP')) { define('DP', '..' . DIRECTORY_SEPARATOR); }

// Launch system configuration
// Запускаем конфигурацию системы

require_once __DIR__ . DS . 'configuration' . DS . 'init.php';

?>