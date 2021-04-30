<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Components\Router;
use is\Model\Parents\Entry;
use is\Model\Databases\Database;

// читаем user

$uri = Uri::getInstance();
$router = Router::getInstance();
$state = State::getInstance();

// если пути нет или была ошибка, то выходим из этого разбора

if (
	!System::typeIterable($uri -> route) ||
	$state -> get('error')
) {
	return;
}

// устанавливаем путь

$route = &$uri -> route;

// продолжаем разбор

$names = $router -> structure -> getNames();
$name = null;
$type = null;
$id = null;

foreach ($route as $key => $item) {
	$name = ($name ? $name . ':' : null) . $item;
	if (Objects::match($names, $name)) {
		$item = $router -> structure -> getByName($name);
		$type = $item -> get('type');
		if ($type === 'content') {
			$id = $key + 1;
			break;
		}
	}
}
unset($key, $item);

if ($type !== 'content') {
	return;
}

$content = Objects::get($route, $id);
$route = Objects::cut($route, $id);

$router -> content = [
	'name' => Objects::last($route, 'value'),
	'array' => $content
];

unset($content, $route, $names, $name, $type, $id);

//echo '<pre>';
//$r1 = $route;
//$r2 = $router -> content;
//$print = Display::getInstance();
//$print -> dump($r1);
//$print -> dump($r2);
//echo '<hr>';
//exit;

?>