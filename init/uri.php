<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Url;
use is\Helpers\Prepare;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();

$uri = Uri::getInstance();
$uri -> init();
$uri -> setInit();

// загружаем последовательность инициализации

$path = new Path(__DIR__ . DS . DP);
$path -> include('uri:base');

// set error and api

$error = $config -> get('error:url');
$api = $config -> get('api:url');

// error and api

if (
	Strings::match('/' . $uri -> path['string'], $error) ||
	Strings::match($uri -> query['string'], $error)
) {
	$state -> set('error', true);
} elseif (
	Strings::match('/' . $uri -> path['string'], $api) ||
	Strings::match($uri -> query['string'], $api)
) {
	$state -> set('api', true);
} else {
	$path -> include('uri:path');
}

// правильное отображение

// слеш на конце
//   папка со слешем на конце - правильно
//   папка без слеша на конце - неправильно
//   файл со слешем на конце - неправильно
//   файл без слеша на конце - правильно
// файл index
// файл .html или другое расширение

// предыдущая страница через куки

if (Sessions::getCookie('current-url') !== $uri -> path['string']) {
	
	$current = Sessions::getCookie('current-url');
	$current = Prepare::clear($current);
	$current = Prepare::script($current);
	$current = Prepare::stripTags($current);
	$current = Prepare::urldecode($current);
	
	Sessions::setCookie('previous-url', $current);
	
	unset($current);
	
	//Sessions::setCookie('previous-url', System::clear(System::cookie('current-url', true), 'urldecode simpleurl'));
	
}

if (
	!$uri -> reload &&
	!Strings::match('/' . $uri -> path['string'], $error) &&
	!Strings::match($uri -> query['string'], $error) &&
	!Strings::match('/' . $uri -> path['string'], $api) &&
	!Strings::match($uri -> query['string'], $api)
) {
	Sessions::setCookie('current-url', $uri -> path['string']);
}

// unset error and api

unset($error, $api);

$uri -> previous = Sessions::getCookie('previous-url');

// reload

if ($uri -> reload) {
	
	if ($config -> get('default:mode') === 'develop') {
		$log = Log::getInstance();
		$log -> data[] = 'system will be redirected from incorrect [' . $uri -> reload . '] request';
		$log -> summary();
		$log -> close();
	}
	
	Sessions::reload($uri -> url, 301);
	
} else {
	Sessions::setHeaderCode(200);
}

$print = Display::getInstance();
$print -> dump($uri);
//echo print_r($uri, 1);

?>