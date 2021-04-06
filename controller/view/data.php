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
use is\Model\Components\Uri;
use is\Model\Components\Language;
use is\Model\Templates\Template;

// читаем конфиг

$config = Config::getInstance();
$router = Router::getInstance();
$uri = Uri::getInstance();
$lang = Language::getInstance();
$template = Template::getInstance();

// настройки вида

$entry = System::typeClass($router -> current, 'entry');

$data = [
	'template' => $router -> template['name'],
	'section' => $router -> template['section'],
	'page' => $entry ? $router -> current -> getEntryData('name') : null,
	'parents' => $entry ? $router -> current -> getEntryKey('parents') : null,
	'type' => $entry ? $router -> current -> getEntryKey('type') : null,
	'route' => $router -> route,
	
	'url' => $uri -> url,
	'domain' => $uri -> domain,
	'home' => !System::typeIterable($uri -> getPathArray()),
	
	'lang' => []
];

$data['lang']['page'] = $data['page'] ? $lang -> get('menu:' . $data['page']) : null;

$parents = $data['parents'];
if (System::typeIterable($parents)) {
	foreach ($parents as $item) {
		$name = $lang -> get('menu:' . $item);
		$data['lang']['parents'][] = $name ? $name : $item;
	}
	unset($item);
}
unset($parents);

$route = $data['route'];
if (System::typeIterable($route)) {
	foreach ($route as $item) {
		$name = $lang -> get('menu:' . $item);
		$data['lang']['route'][] = $name ? $name : $item;
	}
	unset($item);
}
unset($route);

$template -> setData($data);

unset($data);

?>