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

$config = Config::getInstance();
$router = Router::getInstance();

// browser cache

$cache = Parser::fromString( $config -> get('default:cache') );
// not from config - now from structure
// echo '<pre>';
// echo print_r($router, 1);

$set_time = (int) $cache[0] * $config -> get('time:' . $cache[1]);
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
//
//$c = 'now-time   = ' . $now_time . "\r\n" . 
//	 'set-time   = ' . $set_time . "\r\n" . 
//	 'cache-time = ' . $cache_time . "\r\n" . 
//	 'exrires    = ' . $cache_time . "\r\n" . 
//	 'last-mod   = ' . $now_time . "\r\n";
//	file_put_contents(DR . $now_time, $c);

if ($cache_time && $cache_time >= $now_time) {
	Sessions::setHeaderCode(304);
	exit;
} else {
	Sessions::unCookie('is-expires');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $now_time));
}

unset($cache);

?>