<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Paths;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Uri;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Files\File;

// читаем user

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

// Если нет файла, то вообще отменяем этот раздел

if (!$uri -> file) {
	return;
}

// Определяем тип файла на соответствие настройкам роутинга
// И если он совпадает, то чтение файла отменяем - это файл из структуры

$cancel = null;

if ($config -> get('router:folders:convert')) {
	
	$extension = $config -> get('router:folders:extension');
	$extension = $extension ? $extension : 'php';
	
	$index = $config -> get('router:index');
	$index = $index ? $index : 'index';
	
	$add = $config -> get('router:folders:index');
	$add = $add ? '/' . $index : null;
	
	$state -> set('relast', $add . '.' . $extension);
	
	if ($uri -> file['extension'] === $extension) {
		$cancel = true;
	}
	
	unset($extension, $index, $add);
	
}

if ($cancel) {
	return;
}

unset($cancel);

// Определяем наличие файла
// И если запрошенный файл реально существует,
// то читаем его

$data = [
	'name' => $uri -> file['name'],
	'extension' => $uri -> file['extension'],
	'url' => '/' . $uri -> path['string'],
	'real' => $config -> get('path:site') . Paths::toReal($uri -> path['string'])
];

// Сам файл

$file = File::getInstance();
$file -> init($data);

if ($file -> error) {
	$state = State::getInstance();
	$state -> set('error', 404);
	$state -> set('reason', 'file does not exists');
}

unset($data);

//echo '<pre>';
//echo print_r($router, 1);
//echo '</pre>';

?>