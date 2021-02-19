<?php defined('isENGINE') or die;

// переинициализируем драйвера на запись в базу данных

if (defined('isWRITE')) {
	error('403', false, 'is hack attempt to change system constant isWRITE');
}

// в каких случаях разрешена переинициализация:
// 1. это запрос (isPROCESS)
// * если это не запрос, то -
// 2. если это запрос, но запись не защищена, то сразу +
// 3. если запись защищена и сданы все проверки:
// 3.1. в системе заведены пользователи и это разрешенный пользователь
// 3.2. в системе задан администратор и это администратор
// 3.3. этот запрос является системным, например, регистрация пользователя
// * к слову, все запросы от ботов будут являться системными

if (!defined('isPROCESS') || !isPROCESS) {
	define('isWRITE', false);
} elseif (!SECURE_WRITING) {
	define('isWRITE', true);
} elseif (
	isALLOW || isSYSTEM
) {
	define('isWRITE', true);
}

if (isWRITE && SECURE_WRITING) {
	
	if (DB_TYPE === 'local') {
		
		// переинициализация не требуется
		
	} elseif (DB_TYPE === 'csv') {
		
	} elseif (DB_TYPE === 'mysqli') {
		
		// закрываем старое соединение
		
		// и открываем новое
		
		$dbset = [
			'host' => defined('DB_HOST') && DB_HOST ? DB_HOST : 'localhost',
			'user' => DB_WRITINGUSER,
			'pass' => DB_WRITINGPASS,
			'db' => DB_NAME,
			'port' => defined('DB_PORT') && DB_PORT ? DB_PORT : null,
			'charset' => 'utf8'
		];
		
		$db = new SafeMysql($dbset);
		
	} elseif (DB_TYPE === 'pdo') {
		
		// закрываем старое соединение
		
		// и открываем новое
		
	}
	
}

?>