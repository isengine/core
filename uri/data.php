<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\Uri;
use is\Model\Components\Config;

// читаем uri

$config = Config::getInstance();
$uri = Uri::getInstance();

// разбираем данные

$data = [];

$path_array = $uri -> getPathArray();
$find = Objects::find($path_array, $config -> get('url:data:name'));

if ($config -> get('url:data:path') && $find) {
	$array = Objects::get($path_array, $find + 1);
	if ($array) {
		$data = Objects::merge($data, Objects::pairs($array));
	}
}

if ($config -> get('url:data:query')) {
	if (System::server('method') === 'post') {
		$data = Objects::merge($data, $_POST);
	} else {
		$data = Objects::merge($data, $uri -> query['array']);
	}
}

$uri -> setData($data);

unset($data);

//echo print_r($data, 1);
//echo '[' . print_r($match, 1) . ']';
//echo '[' . print_r($find, 1) . ']';

?>