<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Helpers\Match;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Apis\Api;

// инициализация

$path = __DIR__;

System::includes('base', $path);
System::includes('settings', $path);
System::includes('data', $path);
System::includes('launch', $path);
	
?>