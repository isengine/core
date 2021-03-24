<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\Path;
use is\Model\Components\Display;
use is\Model\Components\Log;

// загружаем последовательность инициализации

$path = new Path(__DIR__ . DS . DP);

$path -> include('language:base');
$path -> include('language:data');

?>