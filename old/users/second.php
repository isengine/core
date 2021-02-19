<?php defined('isENGINE') or die;

/*
	поля:
					обычное поле
	authorise		логин, поле, по которому разрешена авторизация
	password		пароль, поле, хэш которого проверяется при заполнении
	dateregister	дата и время регистрации / ctime
	datelastvisit	дата и время последнего визита / mtime
	allow			статус бана
	allowip			список разрешенных ip
	allowagent		список разрешенных устройств/браузеров
*/

// здесь задается глобальный объект $userstable

global $userstable;
$userstable = dbUse('userstable', 'select');

//echo '<pre>' . print_r($userstable, true) . '</pre>';

if (isALLOW) {
	
	// инициализация пользователя со всеми данными
	// а также назначение прав пользователю и их разбор
	init('users', 'data');
	init('users', 'language');
	//echo '<pre>' . print_r($user, true) . '</pre>';
	
	if (SECURE_USERS) {
		// более глубокая проверка пользователя по базе данных
		// инициализация пользователя по базе данных - смотрим привязки к браузерам и ip
		init('users', 'secure');
		init('users', 'allow');
	}
	
	if (DEFAULT_CUSTOM) {
		// инициализируем работу с пользователем кастомного ядра
		init('custom', 'core' . DS . 'users' . DS . 'init');
	}
	
}

// читаем права пользователя
init('users', 'rights');

?>