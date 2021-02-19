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

if (Sessions::getCookie('current-url') !== $uri -> path['string']) {
	
	$current = Sessions::getCookie('current-url');
	$current = Prepare::clear($current);
	$current = Prepare::script($current);
	$current = Prepare::stripTags($current);
	$current = Prepare::urldecode($current);
	
	Sessions::setCookie('previous-url', $current);
	
	unset($current);
	
}

if (
	!$state -> get('error') &&
	!$state -> get('api')
) {
	Sessions::setCookie('current-url', $uri -> path['string']);
}

$uri -> previous = Sessions::getCookie('previous-url');

?>