<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Helpers\Ip;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Components\Router;
use is\Model\Databases\Database;

// читаем user

//$uri = Uri::getInstance();
//$user = User::getInstance();
//$session = Session::getInstance();

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();
$session = Session::getInstance();

$ip = $session -> get('ip');
$array = $router -> getData('secure');

$mode = $array['type'];
$list = $array['list'];

if (!$mode || !System::typeIterable($list)) {
	return;
}

$in_range = Ip::range($ip, $list);

if (
	($mode === 'blacklist' && $in_range) ||
	($mode === 'whitelist' && !$in_range) ||
	($mode === 'develop' && !$in_range)
) {
	$state -> set('error', 401);
	$state -> set('reason', 'access to template by your ip is not allowed');
	$state -> set('blockip', true);
}

unset($ip, $array, $mode, $list, $in_range);

//echo '<pre>';
//echo print_r($router, 1);
//echo '</pre>';

?>