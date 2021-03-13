<?php

// Рабочее пространство имен

namespace is;

use is\Model\Database\Database;
use is\Model\Database\Drivers;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;

// Подготавливаем конфигурацию

$config = Config::getInstance();

$dbset = $config -> getArray('db', true);

//$dbset['driver'] = 'ExcelDB';
//$dbset['driver'] = 'TableDB';
//$dbset['rowkeys'] = '0';
//$dbset['rowskip'] = '1';
//$dbset['encoding'] = 'CP1251';

$db = Database::getInstance();
$db -> init($dbset);

if ($dbset['cache']) {
	//$db -> cache($config -> get('path:cache') . 'db_' . $config -> get('db:name') . DS);
}

//$db -> addFilter('name', 'one:+two:-three:*four:10,5_');
//$db -> addFilter('type', '');
//$db -> addFilter('parents', 'news');
//$db -> addFilter('data:price', 'news');
//$db -> addFilter('data:type', '');
//$db -> addFilter('data:price', 'news'); // field(:data=true/false), values=one:two...(+-*_)
//$db -> addFilter([]); // full array filter как в инструкции

//$db -> driver -> fields('articul', [
//	'exclude' => true
//]);
//$db -> driver -> fields('price', [
//	'convert' => 'array'
//]);
//$db -> driver -> fields('size', [
//	'default' => 'no size'
//]);
//$db -> driver -> fields('resize', [
//	'default' => 'no resize<p>123</p>',
//	'prepare' => [
//		'notags'
//	],
//	'match' => [
//		'type' => 'len',
//		'data' => '1:5'
//	]
//]);

$db -> query('read');
$db -> rights(true);

//$db -> collection('content');
//$db -> launch();
//$db -> data -> sortByEntry('id');

// ТОЛЬКО ДЛЯ ОТЛАДКИ !!!
// Смотрим итоги

//$print = Display::getInstance();
//$print -> dump($dbset);
//$print -> dump($pdb);
//$print -> dump($db);
//$print -> dump($db);

//exit;

?>