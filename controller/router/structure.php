<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Components\Router;
use is\Model\Databases\Database;

// читаем user

$user = User::getInstance();
$session = Session::getInstance();
$state = State::getInstance();
$router = Router::getInstance();

$router -> init();

$structure = $session -> getValue('structure');

if ($structure) {
	
	$structure = json_decode($structure, true);
	$router -> setStructure($structure);
	
} else {
	
	// подгружаем данные из БД
	
	$db = Database::getInstance();
	$db -> collection('structures');
	//$db -> driver -> format('structure');
	//$db -> driver -> addFilter('name', $uname);
	//$db -> driver -> addFilter('data:' . $field, $ukey);
	$db -> launch();
	
	$structure = $db -> data -> getData();
	
	$result = [];
	
	$db -> clear();
	
	if (System::typeIterable($structure)) {
		foreach ($structure as $item) {
			$data = $item -> getEntryData();
			if (System::typeOf($data, 'iterable')) {
				$result = Objects::merge($result, $data);
			}
		}
		unset($key, $item);
	}
	
	if (System::typeOf($result, 'iterable')) {
		$router -> addExtension($state -> get('relast'));
		$router -> parseStructure($result);
	}
	
	unset($result);
	
	$session -> setValue('structure', json_encode( $router -> getStructure() ));
	
}

unset($structure);

//echo '<pre>';
//echo print_r($router, 1);
//echo '</pre>';

?>