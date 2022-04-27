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

// scheme

$scheme = $config->get('default:scheme');

if ($scheme && $scheme !== $uri->scheme) {
    $uri->scheme = $scheme;
}

unset($scheme);

// www

$www = $config->get('default:www');

if (
    $www && !$uri->www
) {
    $uri->host = 'www.' . $uri->host;
} elseif (
    !$www && $uri->www
) {
    $uri->host = substr($uri->host, 4);
}

unset($www);

// set domain

$uri->setDomain();
