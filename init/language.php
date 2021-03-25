<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\Display;
use is\Model\Components\Log;

// загружаем последовательность инициализации

$path = __DIR__ . DS . DP;

System::include('language:base', $path);
System::include('language:data', $path);

?>