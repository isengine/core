<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Helpers\Ip;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Components\Router;
use is\Masters\Database;

// читаем user

//$uri = Uri::getInstance();
//$user = User::getInstance();
//$session = Session::getInstance();

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();
$session = Session::getInstance();

$ip = $session->get('ip');
$array = Objects::merge(
    [
        'type' => null,
        'list' => null
    ],
    $router->getData('secure')
);

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
    $state->set('error', 401);
    $state->set('reason', 'access to template by your ip is not allowed');
    $state->set('blockip', true);
}

unset($ip, $array, $mode, $list, $in_range);

//echo '<pre>';
//echo print_r($router, 1);
//echo '</pre>';
