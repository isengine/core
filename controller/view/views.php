<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Paths;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Router;
use is\Model\Components\Language;
use is\Model\Templates\Template;

// читаем конфиг

$config = Config::getInstance();
$template = Template::getInstance();

// задаем кэширование блоков
// и запрещаем кэширование страниц

$template -> view -> add('pages', false);
$template -> view -> add('blocks', true);

// запускаем обнаружение устройств

$template -> view -> add('detect');

// пример рендеринга css файла
//$result = $template -> render('css', 'filename');
//echo $result;

//$print = Display::getInstance();
//$print -> dump($user -> getData());
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($uri);
//$print -> dump($state);
//$print -> dump($template);
//$print -> dump($router);
//
//exit;

?>