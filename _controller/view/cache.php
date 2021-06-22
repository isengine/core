<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\Router;
use is\Components\Uri;

// читаем

$router = Router::getInstance();

// browser cache

$cache = $router -> current -> data['cache']['browser'];

if (!$cache) { return; }

$config = Config::getInstance();

if ($cache === 'default') {
	$cache = Parser::fromString( $config -> get('default:cache') );
}

$set_time = System::typeIterable($cache) ? (int) $cache[0] * ($cache[1] ? $config -> get('time:' . $cache[1]) : 1) : (int) $cache;
$now_time = time();

if (!$set_time) { return; }

$cache_time = Sessions::getCookie('is-expires');
if (!$cache_time) {
	$cache_time = $now_time + $set_time;
	Sessions::setCookie('is-expires', $cache_time);
}

// включение кэширования в заголовках

$data = [
	'Cache-Control' => 'public',
	'Expires' => gmdate('D, d M Y H:i:s \G\M\T', $cache_time)
];

Sessions::setHeader($data);

// время кэша от последнего изменения страницы

$start_time = $cache_time - $set_time;

$uri = Uri::getInstance();
$page = $config -> get('path:templates') . $router -> template['name'] . DS . 'html' . DS . 'inner' . DS . (System::set($uri -> getRoute()) ? Strings::join($uri -> getRoute(), DS) : 'index') . '.php';
$page_time = file_exists($page) ? filemtime($page) : null;

$template = $config -> get('path:templates') . $router -> template['name'] . DS . 'html' . DS . 'template.php';
$template_time = file_exists($template) ? filemtime($template) : null;

$settings_time = $router -> getData('mtime');

// расчет кэша идет по куки и по дате последней модификации страницы
// если время кэширования еще не вышло и
// последняя модификация страницы была до того времени, когда она была закэширована
// вытаскиваем страницу из кэша

if (
	$cache_time &&
	$cache_time >= $now_time &&
	$page_time < $start_time &&
	$template_time < $start_time &&
	$settings_time < $start_time
) {
	Sessions::setHeaderCode(304);
	exit;
} else {
	Sessions::unCookie('is-expires');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $now_time));
}

unset($cache);

?>