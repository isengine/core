<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Sessions;
use is\Helpers\Url;
use is\Model\Globals\Session;
use is\Model\Components\Config;
use is\Model\Components\State;
use is\Model\Components\Content;
use is\Model\Components\Path;

// читаем сессию

$config = Config::getInstance();
$session = Session::getInstance();
$state = State::getInstance();

$referrer = $session -> get('referrer');
$origin = $session -> get('origin');
$server = '//' . $_SERVER['SERVER_NAME'];

$secure = $config -> get('secure:referrer');

$isorigin = true;

if (
	!$session -> get('agent') ||
	($origin && !Strings::match($origin, $server))
) {
	
	$isorigin = false;
	
} elseif ($referrer && !Strings::match($referrer, $server)) {
	
	// проверяем запросы по списку
	
	if ($secure) {
		
		$data = new Content();
		$data -> setFile('referrer.' . $secure . '.ini');
		$data -> readContent();
		$content = $data -> getContent();
		
		if ($content) {
			$match = Objects::match(
				$content,
				Url::parse($referrer, 'host')
			);
			
			if (
				($secure === 'blacklist' && $match) ||
				($secure === 'whitelist' && !$match)
			) {
				$isorigin = false;
			}
		}
		
	} else {
		$isorigin = false;
	}
	
}

$state -> set('origin', $isorigin);

// Проверяем разрешения на запросы с других сайтов

$request = $config -> get('secure:request');
$method = $session -> get('request');

if (
	// разрешено все
	$config -> get('secure:request') === true ||
	// разрешено то, что указано
	Strings::match($request, $method) !== false
) {
	$state -> set('request', true);
} else {
	// не разрешено
	$state -> set('request', false);
}

// Определяем плохие запросы - запрещенные и из сторонних источников

if ($state -> get('request') && $state -> get('origin')) {
	//System::error('403', false, 'not isREQUEST and not isORIGIN, it was a forbidden request');
}

// Вы можете ограничить запросы следующим способом
// Задав только разрешенные запросы в secure:request, например get
// Задав метод проверки реферреров в secure:referrer, например whitelist
// И внеся в этот whitelist в корне сайта список разрешенных реферреров

?>