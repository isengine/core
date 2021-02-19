<?php

// инициализация работы с базой данных через класс над библиотекой mysqli [NEW]
// что позволило сократить и оптимизировать код, а также использовать стандартные настройки
// теперь не нужно переписывать весь config, а лишь указывать в DB_TYPE 'mysqli:test'

require_once $db -> path . 'safemysql' . DS . 'safemysql.php';

$dbset = [
	'host' => 'localhost',
	'user' => 'root',
	'pass' => '',
	'db' => DB_NAME,
	'port' => null,
	'charset' => 'utf8'
];

if (!$db -> test) {
	$dbset['user'] = DB_USER;
	$dbset['pass'] = DB_PASSWORD;
	if (defined('DB_HOST') && DB_HOST) { $dbset['host'] = DB_HOST; }
	if (defined('DB_PORT') && DB_PORT) { $dbset['port'] = DB_PORT; }
}

$db = new SafeMysql($dbset);

?>