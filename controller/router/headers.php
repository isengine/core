<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Components\State;
use is\Components\Display;
use is\Components\Log;

// читаем uri

$state = State::getInstance();

Sessions::setHeaderCode($state->get('error') ? $state->get('error') : 200);
