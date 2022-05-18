<?php

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
    !System::typeIterable($uri->route)
    || $state->get('error')
) {
    return;
}

// устанавливаем путь

$route = &$uri->route;

// продолжаем разбор

$names = $router->structure->getNames();
$name = null;
$type = null;
$id = null;
$last = null;

foreach ($route as $key => $item) {
    $name = ($name ? $name . ':' : null) . $item;
    if (Objects::match($names, $name)) {
        $item = $router->structure->getByName($name);
        $type = $item->get('type');
        if ($type === 'content') {
            $id = $id === null ? $key : $id;
            $last = $key + 1;
            //break;
        }
    } else {
        break;
    }
}
unset($key, $item);

if ($type !== 'content') {
    return;
}

$array = Objects::reset(Objects::get($route, $id));
$len = Objects::len($array) > 1;

$router->content['name'] = $len ? Objects::last($array, 'value') : null;
$router->content['parents'] = $len ? Objects::unlast($array) : $array;
//'name' => Objects::first(Objects::get($route, $last), 'value'),
//'parents' => Objects::get($route, $id, $last - $id)

$route = Objects::get($route, 0, $last);

unset($route, $names, $name, $type, $id, $last, $array, $len);
