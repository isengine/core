<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Url;
use is\Helpers\Prepare;
use is\Model\Globals\Session;
use is\Model\Components\Uri;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;

// читаем uri

$config = Config::getInstance();

$uri = Uri::getInstance();
$uri -> init();

// scheme

$scheme = $config -> get('default:scheme');

if ($scheme && $scheme !== $uri -> scheme) {
	$uri -> scheme = $scheme;
	$uri -> reload = 'scheme';
}

unset($scheme);

// www

$www = $config -> get('default:www');

if (
	$www && !$uri -> www
) {
	$uri -> host = 'www.' . $uri -> host;
	$uri -> reload = true;
} elseif (
	!$www && $uri -> www
) {
	$uri -> host = substr($uri -> host, 4);
	$uri -> reload = 'www';
}

unset($www);

// lang

$lang = $config -> get('default:lang');

if (
	$lang &&
	!empty($uri -> path['array']) &&
	$lang === Objects::first($uri -> path['array'], 'value')
) {
	$uri -> path['array'] = Objects::unfirst($uri -> path['array']);
	$uri -> setPathString();
	$uri -> setUrl();
	$uri -> reload = 'lang';
}

unset($lang);

// error

$error = $config -> get('error:url');

if (
	Strings::match('/' . $uri -> path['string'], $error) ||
	Strings::match($uri -> query['string'], $error)
) {
	// здесь по-идее должна быть запись ошибки в какую-то константу,
	// чтобы затем перенаправить код на шаблон ошибки
	//
	//System::error(
	//	!empty($uri -> path -> array[1]) ? $uri -> path -> array[1] : '404',
	//	false,
	//	LOG_MODE === 'panic' ? 'redirecting to error from uri' : null
	//);
}

unset($error);

// file

$filter = $config -> get('filter:url');
$type = $uri -> file['extension'];

if (
	$type &&
	!Strings::match('/' . $uri -> path['string'], $filter)
) {
	
	$page = $config -> get('default:extension');
	
	if (
		$uri -> file['name'] === 'index' &&
		(
			$type === 'php' ||
			($page && $type === $page)
		)
	) {
		
		// редирект на папку без индекса
		$uri -> path['array'] = Objects::unlast($uri -> path['array']);
		$uri -> file = [];
		$uri -> setPathString();
		$uri -> setUrl();
		
	} elseif (
		$type === 'php' ||
		$type === 'ini'
	) {
		
		// $error 404
		
	} elseif (
		$page &&
		$type === $page
	) {
		
		// редирект на папку без расширения
		$uri -> path['array'] = Objects::unlast($uri -> path['array']);
		$uri -> path['array'][] = $uri -> file['name'];
		$uri -> file = [];
		$uri -> setPathString();
		$uri -> setUrl();
		
	} elseif (
		(
			$type === 'htm' ||
			$type === 'html' ||
			$type === 'xml' ||
			$type === 'json' ||
			$type === 'txt' ||
			$type === 'script'
		) &&
		$uri -> file['file'] === $uri -> path['string']
	) {
		
		$custon = $config -> get('path:custon') . 'generators' . DS . $uri -> file['file'] . '.php';
		$core = $config -> get('path:core') . 'generators' . DS . $uri -> file['file'] . '.php';
		
		if (file_exists($custom)) {
			
			require_once $custom;
			exit;
			
		} elseif (file_exists($core)) {
			
			require_once $core;
			exit;
			
		} else {
			
			if (empty($uri -> query['string'])) {
				$uri -> query['string'] = '?';
			} else {
				$uri -> query['string'] .= '&';
			}
			
			$api = $config -> get('api:name');
			$api = Strings::split($api, '\/', true);
			$api = Strings::join($api, '/') . '/';
			
			$uri -> path['string'] = $api . 'files/' . $type . '/' . $uri -> path['string'];
			
			$uri -> setPathArray();
			$uri -> setUrl();
			
			unset($api);
			
		}
		
		unset($core, $custom);
		
	}
	
	unset($page);
	
}

unset($filter, $type);

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

$error = $config -> get('error:url');
$filter = $config -> get('filter:url');

if (
	!$uri -> reload &&
	!Strings::match('/' . $uri -> path['string'], $error) &&
	!Strings::match($uri -> query['string'], $error) &&
	!Strings::match('/' . $uri -> path['string'], $filter) &&
	!Strings::match($uri -> query['string'], $filter)
) {
	Sessions::setCookie('current-url', $uri -> path['string']);
}

unset($error, $filter);

$uri -> previous = Sessions::getCookie('previous-url');

// reload

if ($uri -> reload) {
	
	if ($config -> get('default:mode') === 'develop') {
		$log = Log::getInstance();
		$log -> data[] = 'system will be redirected from incorrect [' . $uri -> reload . '] request';
		$log -> summary();
		$log -> close();
	}
	
	System::reload($uri -> url, 301);
	
} else {
	System::setHeaderCode(200);
}

//$print = Display::getInstance();
//$print -> dump($uri);
//echo print_r($uri, 1);

?>