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
	
	// назначаем права по-умолчанию из конфигурации
	
	$config = Config::getInstance();
	$sur = $config -> getArray('db:rights', true);
	unset($config);
	
	// читаем права с выборкой из базы данных
	// последовательно по всем родителям пользователя
	
	$parents = $user -> data -> getEntryKey('parents');
	
	// сюда мы еще вернемся,
	// здесь может быть ошибка из-за того, что родитель один и назначен строкой
	// или родители есть, а прав на них нет
	
	if ($parents) {
		
		$db -> collection('rights');
		$db -> driver -> filter -> methodFilter('or');
		
		foreach ($parents as $item) {
			$db -> driver -> filter -> addFilter('name', $item);
		}
		unset($item);
		
		$db -> launch();
		
		foreach ($parents as $item) {
			$id = $db -> data -> getId($item);
			$data = System::set($id) ? $db -> data -> getDataByName($item) : null;
			$sur = Objects::merge($sur, $data, true);
		}
		unset($item, $id, $data);
		
		$db -> clear();
		
	}
	
	$session -> setValue('rights', json_encode($sur));
	
}

// переназначаем права для базы данных

$db -> rights( $sur, $user -> data -> getEntryKey('name') );

unset($sur);

?>