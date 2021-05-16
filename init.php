<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;

// Базовые константы

if (!defined('isENGINE')) { define('isENGINE', microtime(true)); }
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
if (!defined('DP')) { define('DP', '..' . DIRECTORY_SEPARATOR); }
if (!defined('DR')) { define('DR', realpath(__DIR__ . DS . DP . DP . DP) . DS); }

// Include framework, core extend and classes from config
// Подключаем фреймворк, расширения ядра и классы, вручную прописанные в конфигурации

require_once 'controller' . DS . 'autoload.php';

// Include core controller
// Подключаем контроллер ядра

require_once 'controller' . DS . 'controller.php';

exit;

?>