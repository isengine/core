<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Sessions;
use is\Model\Globals\Session;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;

// читаем сессию

$session = Session::getInstance();
$session -> init();

?>