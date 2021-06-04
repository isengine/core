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
$config = Config::getInstance();

// здесь расположен базовый обработчик роутинга

$uri -> resetRoute();
$path = $uri -> getRoute();

// определяем данные по rest и отделяем их от остального пути

if (System::typeIterable($path)) {
	$find = Objects::find($path, $config -> get('url:data:rest'));
	if (System::set($find)) {
		$uri -> route = Objects::get($path, 0, $find);
	}
	unset($find);
}

?>