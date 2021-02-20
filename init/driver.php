<?php

// Рабочее пространство имен

namespace is;

use is\Controller\Database;
use is\Controller\Drivers;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;

// Подготавливаем конфигурацию

$config = Config::getInstance();

$dbset = [
	'driver' => null,
	'host' => null,
	'name' => null,
	'user' => null,
	'password' => null,
	'prefix' => null,
	'port' => null
];

foreach ($dbset as $key => &$item) {
	$item = $config -> get('db:' . $key);
}
unset($key, $item);

$dbset['driver'] = '\\is\\Controller\\Drivers\\' . $dbset['driver'];
$driver = new $dbset['driver'] ($dbset);
$db = Database::getInstance();
$db -> init($driver);

// ТОЛЬКО ДЛЯ ОТЛАДКИ !!!
// Смотрим итоги

$print = Display::getInstance();
$print -> dump($dbset);
$print -> dump($driver);
$print -> dump($db);

exit;

?>