<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Components\Display;
use is\Masters\View;

$display = Display::getInstance();
$view = View::getInstance();

// запускаем поддержку вывода

$view->set('display', $display);
