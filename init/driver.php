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


/*
$db = Database::getInstance();
$db -> init($dbset);
$db -> cache($config -> get('path:cache') . 'db_' . $config -> get('db:name') . DS);

$db -> collection('content');
$db -> query('read');

//$db -> filter('name', 'one:+two:-three:*four:10,5_');
//$db -> filter('type', '');
$db -> filter('parents', 'news');
//$db -> filter('data:price', 'news');
//$db -> filter('data:type', '');
//$db -> filter('data:price', 'news'); // field(:data=true/false), values=one:two...(+-*_)
//$db -> filter([]); // full array filter как в инструкции

$db -> launch();
$db -> data -> sortByEntry('id');
*/

$dbset['driver'] = 'ExcelDB';
//$dbset['driver'] = 'TableDB';
$dbset['rowkeys'] = '0';
$dbset['rowskip'] = '1';
//$dbset['encoding'] = 'CP1251';

$db = Database::getInstance();
$db -> init($dbset);
$db -> collection('catalog');
$db -> query('read');
$db -> rights(true);
$db -> cache($config -> get('path:cache') . 'db_' . $config -> get('db:name') . DS);

//$driver = &$db -> driver;

$db -> launch();

// ТОЛЬКО ДЛЯ ОТЛАДКИ !!!
// Смотрим итоги

$print = Display::getInstance();
//$print -> dump($dbset);
//$print -> dump($pdb);
$print -> dump($db);

//exit;

?>