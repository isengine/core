<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;

// Базовые константы

if (!defined('isENGINE')) { define('isENGINE', microtime(true)); }
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
if (!defined('DP')) { define('DP', '..' . DIRECTORY_SEPARATOR); }
if (!defined('DR')) { define('DR', realpath(__DIR__ . DS . DP . DP . DP) . DS); }

// Include loading framework, core extend and classes from config
// Подключаем загрузку фреймворка, расширений ядра и классов, вручную прописанных в конфигурации

require_once 'Controller' . DS . 'autoload.php';

// Include core controller
// Подключаем контроллер ядра

require_once 'Controller' . DS . 'controller.php';

exit;

?>