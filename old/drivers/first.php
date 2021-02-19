<?php defined('isENGINE') or die;

// инициализируем функции по работе с базой данных
// сначала идут функции чтения
// вместе с драйверами грузится обработчик ini-файлов и данных в формате json
// функция записи инициализируется только после всех проверок

// если задан SECURE_WRITING, то после всех проверок пользователь сменяется на DB_WRITINGUSER с паролем DB_WRITINGPASS
// если SECURE_WRITING не задан или false, то пользователь не меняется, но это обеспечивает меньшую безопасность

// инициализация работы с базами данных

if (defined('DB_TYPE') && DB_TYPE) {
	
	$dbtype = dataParse(DB_TYPE);
	
	global $db;
	
	$db = (object) [
		'type' => $dbtype[0],
		'test' => !empty($dbtype[1]) ? true : false,
		'path' => PATH_CORE . 'drivers' . DS . $dbtype[0] . DS
	];
	
	unset($dbtype);
	
	if (
		!file_exists($db -> path . 'init.php') ||
		!file_exists($db -> path . 'driver.php')
	) {
		error('db_driver', null, true);
		//...ошибка - драйвер не найден		
	}
	
	// языковой пакет ошибок
	// (зачем он теперь нужен ???)
	$dberrors = (object) [
		'block' => '<p style="text-align: center; display: block; margin: 10px auto; padding: 10px; background: #F44336; color: white; width: 50%;">',
		'unsupport' => (object) [
			'ru' => 'Ошибка: Неподдерживаемый тип базы данных!',
			'en' => 'Error: Unsupported database type!',
		],
		'connect' => (object) [
			'ru' => 'Ошибка: Невозможно подключиться к базе данных!',
			'en' => 'Error: Unable to database connect!',
		],
		'charset' => (object) [
			'ru' => 'Ошибка: Не установлена кодировка соединения!',
			'en' => 'Error: Charset database not set!',
		],
		'query' => (object) [
			'ru' => 'Ошибка: Ошибка в запросе в базу данных!',
			'en' => 'Error: Invalid database query!',
		],
	];
	
	require_once $db -> path . 'init.php';
	require_once $db -> path . 'driver.php';
	
	unset($db);
	
} else {
	
	error('db_noset', null, true);
	//...ошибка - драйвер не задан
	
}

?>