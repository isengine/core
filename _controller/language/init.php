<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Components\Display;
use is\Components\Log;

// загружаем последовательность инициализации

$path = __DIR__;

System::includes('base', $path);
System::includes('data', $path);

?>