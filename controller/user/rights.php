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

// так - правильно:
if ($sur) {
// так - неправильно, только для тестирования, НЕ ЗАБЫТЬ УБРАТЬ:
//if (!$sur) {
	
	$sur = json_decode($sur, true);
	
} else {
	
	// назначаем права по-умолчанию из конфигурации
	// $config = Config::getInstance();
	// $sur = $config -> getArray('db:rights', true);
	// unset($config);
	
	// назначение прав из конфигурации устарело
	// т.к. оно проходит в начале инициализации драйвера базы данных
	// здесь мы должны опираться исключительно на уже заданные права
	$sur = $db -> driver -> rights;
	
	// читаем права с выборкой из базы данных
	// последовательно по всем родителям пользователя
	
	$parents = $user -> data -> getEntryKey('parents');
	//$parents = Objects::add(['default'], $user -> data -> getEntryKey('parents'));
	// defaults мы ввели как временную меру по ограничению прав по-умолчанию, теперь это не нужно
	
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
			
			// какая-то странная штука с проверкой айдишников
			$id = $db -> data -> getId($item);
			$data = System::set($id) ? $db -> data -> getDataByName($item) : null;
			// почему нельзя просто
			// $data = $db -> data -> getDataByName($item);
			// непонятно
			// может, это связано с тем, что какие-то права могут попасть в дефолтную выборку
			
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