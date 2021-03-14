<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Error;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();

// инициализация

$error = Error::getInstance();
$error -> init($config -> get('url:error:url'));
$error -> prefix = $config -> get('url:error:prefix');
$error -> postfix = $config -> get('url:error:postfix');

$path = $error -> path . $error -> prefix;
$find = System::server('request');
$code = mb_substr(
	$find,
	Strings::find($find, $path) + Strings::len($path),
	3
);

// error из пути, согласно заданным настройкам

if ($path && $code && Strings::match($find, $path)) {
	$state -> set('error', $code);
}

// error из первого параметра get, согласно общепринятой совместимости с настройками из файла htaccess

if (
	!empty($_GET['error']) &&
	Objects::first($_GET, 'key') === 'error'
) {
	$state -> set('error', $_GET['error']);
}

unset($path, $find, $code);

?>