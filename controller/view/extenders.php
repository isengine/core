<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Paths;
use is\Components\Config;
use is\Components\State;
use is\Components\Display;
use is\Components\Log;
use is\Components\Router;
use is\Components\Language;
use is\Masters\View;

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
$view -> get('lang') -> add(
	'this',
	$config -> get('path:templates') . $router -> template['name'] . DS . 'lang' . DS . $view -> get('state|lang') . '.ini'
);

$view -> add('translit');

// запускаем иконки

$view -> add('icon');

// задаем рендеринг

$view -> add('render');

$from = [
	$config -> get('path:templates') . $router -> template['name'] . DS,
	DS
];
$to = [
	DI . Paths::toReal(Paths::clearSlashes($config -> get('url:assets'))) . DS,
	DS . Paths::toReal($router -> template['name']) . DS
];
$url = [
	'/' . Paths::clearSlashes($config -> get('url:assets')) . '/',
	'/' . $router -> template['name'] . '/'
];

$view -> get('render') -> init($from, $to, $url);

// запускаем обнаружение устройств

$view -> add('device');

// запускаем процессы обработки текстовых переменных

// вообще, здесь нужно сделать кэширование языков и проверять язык
// это даст сильную экономию ресурсов,
// т.к. не нужно будет пробегать весь языковой массив
// но это кэширование нужно каким-то образом сочетать с предыдущим кэшированием
// еще неплохо было бы вынести всю работу с языком отдельно, уже после tvars
// но нужно посмотреть, не используют ли язык предыдущие штуки
// или тогда поднять tvars выше

$view -> add('tvars');

$view -> get('lang') -> setData(
	$view -> get('tvars') -> launch(
		$view -> get('lang') -> getData()
	)
);

// запускаем процессы отрисовки изображений

$view -> add('img');

// запускаем реактивность

if ($config -> get('develop:enable') && $config -> get('develop:reactive')) {
	$view -> add('reactive');
	
	$folder = $view -> get('state|real');
	$view -> get('reactive') -> launch($folder, $config -> get('develop:reactive'));
	
	if (isset($_GET['reactive'])) {
		echo $view -> get('reactive') -> getList();
		exit;
	}
}

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