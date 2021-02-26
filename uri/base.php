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

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

// ini

if ($uri -> file['extension'] === 'ini') {
	$state -> set('error', 404);
}

// scheme

$scheme = $config -> get('default:scheme');

if ($scheme && $scheme !== $uri -> scheme) {
	$uri -> scheme = $scheme;
}

unset($scheme);

// www

$www = $config -> get('default:www');

if (
	$www && !$uri -> www
) {
	$uri -> host = 'www.' . $uri -> host;
} elseif (
	!$www && $uri -> www
) {
	$uri -> host = substr($uri -> host, 4);
}

unset($www);

// lang

$lang = $config -> get('default:lang');

if (
	$lang &&
	!empty($uri -> path['array']) &&
	$lang === Objects::first($uri -> path['array'], 'value')
) {
	$uri -> path['array'] = Objects::unfirst($uri -> path['array']);
	$uri -> setFromArray();
}

unset($lang);

?>