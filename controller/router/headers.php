<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Model\Components\State;
use is\Model\Components\Display;
use is\Model\Components\Log;

// читаем uri

$state = State::getInstance();

Sessions::setHeaderCode($state -> get('error') ? $state -> get('error') : 200);

?>