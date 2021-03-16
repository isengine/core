<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Sessions;
use is\Helpers\Paths;
use is\Helpers\System;
use is\Helpers\Match;
use is\Model\Components\Session;
use is\Model\Components\Config;
use is\Model\Components\State;
use is\Model\Components\Content;
use is\Model\Components\Path;

// читаем сессию

$config = Config::getInstance();
$session = Session::getInstance();
$state = State::getInstance();

$origin = $session -> get('origin');
$referrer = $origin ? $origin : $session -> get('referrer');
$server = '//' . System::server('host');
$secure = $config -> get('secure:referrer');

$isreferrer = true;

if ($referrer && !Strings::match($referrer, $server)) {
	
	// проверяем запросы по списку
	
	if ($secure) {
		
		$data = new Content('config');
		$data -> setFile('referrer.' . $secure . '.ini');
		$data -> readContent();
		$content = $data -> getContent();
		
		if ($content) {
			
			$match = Match::maskOf(
				Paths::parseUrl($referrer, 'host'),
				$content,
				false
			);
			
			if (
				($secure === 'blacklist' && $match) ||
				($secure === 'whitelist' && !$match)
			) {
				$isreferrer = false;
			}
			
		}
		
	} else {
		$isreferrer = false;
	}
	
} elseif (!$session -> get('agent')) {
	$isreferrer = false;
}

// Проверяем разрешения на запросы с других сайтов

$request = $config -> get('secure:request');
$method = $session -> get('request');

// разрешено все или разрешено то, что указано

$isrequest = $config -> get('secure:request') === true || Strings::match($request, $method);

// определяем, хороший или плохой запрос
// плохие запросы - запрещенные и из сторонних источников

$state -> set('request', $isrequest || $isreferrer);

if (!$state -> get('request')) {
	$state -> set('error', 403);
	$state -> set('reason', 'it was a forbidden request - not allowed method or from not allowed referrer');
}

// Вы можете ограничить запросы следующим способом
// Задав только разрешенные запросы в secure:request, например get
// Задав метод проверки реферреров в secure:referrer, например whitelist
// И внеся в этот whitelist в корне сайта список разрешенных реферреров

?>