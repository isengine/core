<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Helpers\Matches;
use is\Helpers\Sessions;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\Uri;
use is\Components\State;
use is\Masters\Api;

// инициализация

$state = State::getInstance();

if ($state->get('error')) {
    Sessions::setHeaderCode($state->get('error'));
    return;
}

$path = __DIR__;

System::includes('base', $path);
System::includes('settings', $path);
System::includes('data', $path);
System::includes('launch', $path);
