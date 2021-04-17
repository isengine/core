<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Router;

// читаем

$router = Router::getInstance();

// browser cache

$cache = $router -> current -> getData('cache')['browser'];

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

// проверка даты последней модификации страницы
// если последняя модификация была давно, то вытаскиваем страницу из кэша
// если же нет, то читаем страницу заново
//if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
//	$cache_modified = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
//} elseif (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) {
//	$cache_modified = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));  
//} else {
//	$cache_modified = null;
//}
// теперь расчет кэша идет по куки

if ($cache_time && $cache_time >= $now_time) {
	Sessions::setHeaderCode(304);
	exit;
} else {
	Sessions::unCookie('is-expires');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $now_time));
}

unset($cache);

?>