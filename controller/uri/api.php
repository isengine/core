<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Helpers\Matches;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\Uri;
use is\Components\State;

// инициализация

$config = Config::getInstance();
$uri = Uri::getInstance();
$state = State::getInstance();

$api_name = $config->get('api:name');
$api_key = $config->get('api:key');

if (
    $config->get('api:server')
    || (
        $api_name
        && (
            $uri->getPathArray(0) === $api_name
            || $uri->getPathArray(1) === $api_name
        )
    )
) {
    $key = $uri->getData($api_key);
    $state->set('api', $key ? $key : true);
    unset($key);
} else {
    $state->set('api', false);
}

$uri->deleteDataKey($api_key);

unset($api_name, $api_key);
