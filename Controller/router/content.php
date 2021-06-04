<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Components\Router;
use is\Parents\Entry;
use is\Masters\Database;

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
$id_last = null;

foreach ($route as $key => $item) {
	$name = ($name ? $name . ':' : null) . $item;
	if (Objects::match($names, $name)) {
		$item = $router -> structure -> getByName($name);
		$type = $item -> get('type');
		if ($type === 'content') {
			$id = $id === null ? $key : $id;
			$id_last = $key + 1;
			//break;
		}
	}
}
unset($key, $item);

if ($type !== 'content') {
	return;
}

$item = Objects::last(Objects::get($route, $id_last));

$router -> content = [
	'name' => Objects::last(Objects::cut($route, $id + 1), 'value'),
	'array' => Objects::get($route, $id, $id + $id_last),
	// name and array now is deprecated
	'parents' => Objects::get($route, $id, $item['key']),
	'item' => $item['value']
];

$route = Objects::cut($route, $id_last);

unset($route, $names, $name, $type, $id);

//echo '<pre>';
//$r1 = $route;
//$r2 = $router -> content;
//$print = Display::getInstance();
//$print -> dump($r1);
//$print -> dump($r2);
//echo '<hr>';
//exit;

?>