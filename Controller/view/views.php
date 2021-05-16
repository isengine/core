<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Paths;
use is\Model\Components\Config;
use is\Model\Components\State;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Router;
use is\Model\Components\Language;
use is\Model\Masters\View;

// читаем конфиг

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();
$view = View::getInstance();

// читаем базовые состояния

$view -> add('state');
$view -> add('vars');

// читаем контент

if (System::set($router -> content)) {
	$view -> add('content');
}

// читаем настройки seo

$view -> add('seo');
$view -> get('seo') -> title();
$view -> get('seo') -> keys();

// запускаем языки

$view -> add('lang');

// запускаем иконки

$view -> add('icon');

// задаем рендеринг

$view -> add('render');

$from = [
	$config -> get('path:templates') . $router -> template['name'] . DS,
	DS
];
$to = [
	$config -> get('path:site') . Paths::toReal(Paths::clearSlashes($config -> get('url:assets'))) . DS,
	DS . Paths::toReal($router -> template['name']) . DS
];
$url = [
	'/' . Paths::clearSlashes($config -> get('url:assets')) . '/',
	'/' . $router -> template['name'] . '/'
];

$view -> get('render') -> init($from, $to, $url);

// запускаем обнаружение устройств

$view -> add('detect');

// запускаем процессы обработки текстовых переменных

$view -> add('process');

// запускаем специальные группы

$view -> add('special');
$view -> get('special') -> init( $view -> get('state|settings:special') );

// инициализируем шаблонизатор с параметрами

// задаем кэширование блоков
// и запрещаем кэширование страниц

$path = $config -> get('path:templates');
$cache = $config -> get('path:cache') . 'templates' . DS;

$view -> add('layout');

$view -> get('layout') -> init('pages', $path, $cache);
$view -> get('layout') -> init('blocks', $path, $cache); // переключить на true

// не хватает layout-а для остальных страниц, не inner,
// например, wrapper, common и других

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
//$print -> dump($router -> content);
//$print -> dump($view -> get('content'));
//$print -> dump($router -> current);
//
//exit;

?>