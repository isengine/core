<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Masters\Database;
use is\Components\Config;
use is\Components\Display;

// Подготавливаем конфигурацию

$config = Config::getInstance();

// Задаем права по-умолчанию

$dbset = $config -> getArray('db', true);

//$dbset['driver'] = 'excel';
//$dbset['driver'] = 'table';
//$dbset['rowkeys'] = '0';
//$dbset['rowskip'] = '1';
//$dbset['encoding'] = 'CP1251';

$db = Database::getInstance();
$db -> init($dbset);

if ($config -> get('cache:db')) {
	$db -> cache($config -> get('path:cache') . 'database' . DS . $config -> get('db:name') . DS);
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

// здесь мы добавляем новое условие, которого не было раньше
// оно связано с тем, что в конфигурации теперь можно назначать
// имя группы прав по-умолчанию
// и если это так, то сначала права на чтение разрешаются
// но только затем, чтобы прочесть группу прав
// и установить права из нее

$rights = $config -> get('db:rights');

if (System::type($rights) !== 'true') {
	
	if ($rights) {
		
		// если права - это строка
		
		$db -> rights(true);
		
		$db -> collection('rights');
		$db -> driver -> filter -> addFilter('name', $rights);
		$db -> launch();
		
		$rights = $db -> data -> getDataByName($rights);
		
		$db -> clear();
		
	} else {
		// пробуем прочесть права как массив прав
		$rights = $config -> getArray('db:rights', true);
	}
	
}

$db -> rights($rights);

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