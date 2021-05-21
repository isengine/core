<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Helpers\Match;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\Uri;
use is\Components\State;
use is\Masters\Api;

// инициализация

$path = __DIR__;

System::includes('base', $path);
System::includes('settings', $path);
System::includes('data', $path);
System::includes('launch', $path);
	
?>