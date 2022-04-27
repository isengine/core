<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Components\Display;
use is\Components\Log;
use is\Masters\View;

// читаем

$view = View::getInstance();

// код

// запускаем рендеринг папок

$array = $view->get('state|settings:folders');

if (!System::typeIterable($array)) {
    return;
}

$array = Objects::clear($array, true);

foreach ($array as $item) {
    $view->get('render')->launch('folder', $item);
}
unset($item, $array);

//exit;
