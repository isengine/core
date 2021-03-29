<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;

// Базовые константы

if (!defined('isENGINE')) { define('isENGINE', microtime(true)); }
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
if (!defined('DP')) { define('DP', '..' . DIRECTORY_SEPARATOR); }
if (!defined('DR')) { define('DR', realpath(__DIR__ . DS . DP . DP . DP) . DS); }

// Include framework and core extend
// Подключаем фреймворк и расширения ядра

$autoload = DR . 'vendor' . DS . 'autoload.php';
$framework = DR . 'vendor' . DS . 'isengine' . DS . 'framework' . DS . 'php' . DS . 'init.php';
$composer = file_exists($autoload);

require_once $composer ? $autoload : $framework;

if (!$composer) {
	require_once 'model' . DS . 'model.php';
}

unset($autoload, $framework);

// Include core controller
// Подключаем контроллер ядра

require_once 'controller' . DS . 'controller.php';

// test set

echo '<!--noindex--><hr><p>END OF TESTS<br>' . number_format(memory_get_usage() / 1024, 3, '.', ' ') . ' KB total / ' . number_format(memory_get_peak_usage() / 1024, 3, '.', ' ') . ' KB in peak<br>' . number_format(microtime(true) - isENGINE, 3, null, null) . ' sec is speed</p><!--/noindex-->';

exit;

?>