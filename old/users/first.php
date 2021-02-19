<?php defined('isENGINE') or die;

init('functions', 'ip');
init('functions', 'users');

// первым делом создаем данные пользователя

//$err = '';

// проверка константы здесь - обязательно,
// так как если она была объявлена ранее,
// налицо попытка взлома

if (defined('isALLOW')) {
	error('403', true, 'is hack attempt to change system constant isALLOW');
}

if (cookie('SID', true) || cookie('UID', true)) {
	
	session_start();
	define('isALLOW', true);
	
} else {
	
	// объявить константы здесь - обязательно,
	// так как это защищает от возможности изменить их в дальнейшем коде
	define('isALLOW', false);
	
}

// а здесь мы инициализируем базовые данные пользователя
global $user;
//logging('first - init');
userSet();

if (isALLOW) {
	
	if (
		cookie('SID', true) !== $user -> sid ||
		cookie('UID', true) !== $user -> uid ||
		//!set($_SESSION['secure']) || // вот это здесь под очень большим вопросом - две проверки, причем одна ниже, но та правильнее, т.к. зависит от константы, а эта срабатывает всегда!
		!isORIGIN
	) {
		
		//print_r($_COOKIE);
		//print_r($user);
		//exit;
		
		userUnset();
		error('403', true, 'bad SID or UID or not is origin, see php session configuration and maybe session or cookies were stolen');
		//$err .= '<br>ERROR 403!<br>';
	}
	
	//$err .= 'SID: ' . cookie('SID', true) . ' / ' . $user -> sid . ' // UID: ' . cookie('UID', true) . ' / ' . $user -> uid . '<br>';
	
	if (SECURE_SESSIONTIME) {
		
		if (
			time() > crypting($user -> token, true) + (int) SECURE_SESSIONTIME ||
			time() < crypting($user -> token, true)
		) {
			
			// удаляем пользователя из базы данных / удаляем файл и записываем нового
			// но для этого нужно подключить функцию записи/удаления из базы
			// еще будет идти проверка на существование пользователя (по id/name) в базе данных,
			// и если такого нет, любой запрос будет отвергнут
			
			//$err .= 'SMENA COOKIE!<br>';
			
			if (!set($_SESSION['secure'])) {
				userUnset();
				/*
				session_destroy();
				unset($_SESSION);
				cookie(['SID', 'UID']);
				*/
				error('403', true, 'wrong session when it was regenerate, maybe session or cookies were stolen');
			}
			
			session_regenerate_id(true);
			$_SESSION['token'] = null;
			
			if (SECURE_CSRF) {
				$_SESSION['csrf'] = null;
			}
			
			//logging('first - smena cookie');
			userSet();
			
			cookie('SID', session_id());
			cookie('UID', $user -> uid);
			
		}
	}
	
}

?>