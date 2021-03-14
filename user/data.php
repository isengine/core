<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Databases\Database;

// читаем user

$user = User::getInstance();
$session = Session::getInstance();

// проверяем сессию на наличие данных пользователя

$su = $session -> getValue('user');

if ($su) {
	
	$user -> data -> setEntry( json_decode($su, true) );
	
} else {
	
	// либо так, либо будет подгружаться пользователь по-умолчанию
	
	$session = Session::getInstance();
	$session -> reset();
	
	$state -> set('error', 403);
	$state -> set('reason', 'user data or user name not set in session and cannot be read from database');
	
}

unset($su);

/*
это все нафиг не нужно, по большому счету
во-первых, затрудняет понимание
во-вторых, лишние данные

} elseif ($user -> data -> getData()) {
	
	// это тоже бессмысленное условие - где взять данные, которые изначально пустые
	// и они заполняются только при авторизации
	
	// если нет записи данных пользователя в сессии,
	// это значит, что мы прочли их из базы данных,
	// и теперь их нужно записать в сессию
	
	$_SESSION['user'] = json_encode( $user -> data -> getEntry() );
	
} elseif (!empty($_SESSION['username'])) {
	
	// читаем настройки полей пользователя
	
	$db = Database::getInstance();
	$db -> collection('users');
	$db -> driver -> addFilter('type', '-settings');
	$db -> driver -> addFilter('name', $_SESSION['username']);
	$db -> launch();
	
	$user -> data -> setEntry($db -> data -> getFirst());
	
	$db -> clear();
*/

	/*
	// а вот этот код нужно вставить в инициализацию пользователя
	// но после инициализации, имя пользователя должно быть прописано
	// в сессии и в свойстве name
	
	if (!$user -> data && $user -> special) {
		
		// если не получилось - не беда
		// теперь мы ищем все поля в данных, по которым разрешена авторизация
		
		$user -> special['authorise']
		
		$db -> driver -> methodFilter('or');
		
		foreach ($user -> special as $item) {
			$db -> driver -> addFilter('data:' . $item, $_SESSION['username']);
		}
		unset($item);
		
		$db -> launch();
		
		$user -> setData($db -> data -> getFirstData());
		
		$db -> clear();
		
	}
	*/
	
	/*
	} elseif (count($try) > 1) {
		
		// здесь нам, в общем-то, не важны совпадения по значениям полей
		// если вы задаете поле с разрешением для авторизации,
		// позаботьтесь о том, чтобы в характеристиках оно было уникальным,
		// хотя позже мы наверняка уберем уникальность данных для полей без авторизации,
		// а для полей авторизации реализуем в системе автоматическую проверку уникальности
		
		// тем не менее, уникальное поле влияет лишь на запись данных
		// например, при регистрации пользователя или при редактировании его профиля,
		// или, например, если пользователь забыл логин и пароль
		// в любом случае, проверка должна осуществляться не здесь
		
		// однако сюда мы все же добавляем проверку,
		// что если обнаружено несколько пользователей, то система выдает ошибку -
		// это защитит от какого-либо несанкционированного взлома
		// или, по крайней мере, от случайного доступа к чужому аккаунту
		
		error('403', true, 'more than one user with specified name or value of another authorised field was found in database');
		
	}
	*/

?>