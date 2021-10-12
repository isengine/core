<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Paths;
use is\Components\Config;
use is\Components\User;
use is\Masters\View;
use is\Masters\Block;

// читаем конфиг

$config = Config::getInstance();
$view = View::getInstance();

$user = User::getInstance();
$user = $user -> getData();

$array = [
	$config -> get('path:templates'),
	$view -> get('state|template'),
	[
		System::server('ip'),
		System::server('agent'),
		System::server('method'),
		$view -> get('state|lang') . '-' . $view -> get('state|code'),
		$user -> getEntryKey('id'),
		$user -> getEntryKey('name'),
		$user -> getEntryKey('type'),
		$user -> getEntryKey('parents')
	],
	$config -> get('path:cache') . 'templates' . DS
];

// запускаем поддержку блоков

$block = new Block;
$block -> init(
	[
		$array[0],
		'html' . DS . 'blocks' . DS,
	],
	$array[1],
	$array[2],
	$array[3],
	$config -> get('cache:blocks')
);
$view -> set('block', $block);

// запускаем поддержку страниц

$page = new Block;
$page -> init(
	[
		$array[0],
		'html' . DS . 'inner' . DS,
	],
	$array[1],
	$array[2],
	$array[3],
	$config -> get('cache:pages')
);
$view -> set('page', $page);

// пример запуска:
// $view -> get('block') -> launch('block:name:with:or:without:path', 'template', $cache = true/null/'default');
// $view -> get('block') -> launch('items:opening', 'default');
// $view -> get('block') -> launch('header');
// раньше:
// $view -> get('layout') -> launch('blocks:default', 'items:opening');



// НИЖЕ КОД СТАРЫЙ, ПОМЕЧЕН КАК УСТАРЕВШИЙ
// НЕ ПОДДЕРЖИВАЕТ НОВОЕ ПРАВИЛЬНОЕ КЕШИРОВАНИЕ
// В РЕЛИЗЕ БУДЕТ ОТКЛЮЧЕН

// инициализируем шаблонизатор с параметрами

// задаем кэширование блоков
// и запрещаем кэширование страниц

$path = $config -> get('path:templates');
$cache = $config -> get('path:cache') . 'templates' . DS;

$view -> add('layout');

$view -> get('layout') -> init('pages', $path, $cache); // true, false, skip для пропуска по-умолчанию
$view -> get('layout') -> init('blocks', $path, $cache); // переключить на true

// не хватает layout-а для остальных страниц, не inner,
// например, wrapper, common и других

?>