<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Url;
use is\Helpers\Prepare;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Error;

// читаем uri
$state = State::getInstance();
$uri = Uri::getInstance();

$error = Error::getInstance();

$epath = $error -> path . $error -> prefix;
$efind = '/' . $uri -> path['string'] . $uri -> query['string'];
$ecode = mb_substr(
	$efind,
	Strings::find($efind, $epath) + Strings::len($epath),
	3
);

// error из пути, согласно заданным настройкам

if (
	$epath && $ecode &&
	(
		Strings::match('/' . $uri -> path['string'], $epath) ||
		Strings::match('/' . $uri -> query['string'], $epath)
	)
) {
	$state -> set('error', $ecode);
}

// error из первого параметра get, согласно общепринятой совместимости с настройками из файла htaccess

if (
	!empty($_GET['error']) &&
	Objects::first($_GET, 'key') === 'error'
) {
	$state -> set('error', $_GET['error']);
}

?>