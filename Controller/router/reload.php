<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Display;
use is\Model\Components\Log;

// читаем uri

$uri = Uri::getInstance();
$state = State::getInstance();

// сравниваем урлы и разрешаем релоад
// только если не была установлена ошибка
// и только если релоад задан в настройках

if ($uri -> url !== $uri -> original) {
	Sessions::reload($uri -> url, $state -> get('reload') === 'temporary' ? null : 301);
}

?>