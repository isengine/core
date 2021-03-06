<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Masters\Database;

// читаем user

$user = User::getInstance();
$session = Session::getInstance();
$db = Database::getInstance();

// проверяем сессию на наличие прав пользователя

$sur = $session -> getValue('rights');

if ($sur) {
	
	$sur = json_decode($sur, true);
	
} else {
	
	// читаем права с выборкой из базы данных
	// последовательно по всем родителям пользователя
	
	$sur = [];
	$array = ['default'];
	$parents = $user -> data -> getEntryKey('parents');
	
	if ($parents) {
		$array = Objects::add(['default'], $parents);
	}
	
	$db -> collection('rights');
	$db -> driver -> filter -> methodFilter('or');
	
	foreach ($array as $item) {
		$db -> driver -> filter -> addFilter('name', $item);
	}
	unset($item);
	
	$db -> launch();
	
	foreach ($array as $item) {
		$id = $db -> data -> getId($item);
		$data = System::set($id) ? $db -> data -> getDataByName($item) : null;
		$sur = Objects::merge($sur, $data, true);
	}
	unset($item, $id, $data);
	
	$db -> clear();
	
	// если права все еще пустые
	// назначаем права по-умолчанию из конфигурации
	
	if (!$sur) {
		
		$config = Config::getInstance();
		$def = $config -> get('users:rights');
		
		$sur = [
			'read' => true,
			'write' => $def,
			'create' => $def,
			'delete' => $def
		];
		
		unset($def);
		
	}
	
	$session -> setValue('rights', json_encode($sur));
	
}

// назначаем права пользователю

$user -> setRights($sur);

// переназначаем права для базы данных

$db -> rights( $sur, $user -> data -> getEntryKey('name') );

unset($sur);

?>