<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();

if (Sessions::getCookie('current-url') !== $uri->url) {
    $current = Sessions::getCookie('current-url');
    if ($current) {
        $current = Prepare::clear($current);
        $current = Prepare::script($current);
        $current = Prepare::stripTags($current);
        $current = Prepare::urldecode($current);
    } else {
        $current = '';
    }
    Sessions::setCookie('previous-url', $current);
    unset($current);
}

if (!$state->get('error')) {
    Sessions::setCookie('current-url', $uri->url);
}

$uri->previous = Sessions::getCookie('previous-url');
