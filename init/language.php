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

System::includes('language:base', $path);
System::includes('language:data', $path);

?>