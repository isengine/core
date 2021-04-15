<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\State;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Files\File;

// читаем user

$file = File::getInstance();
$file -> init();

if ($file -> error) {
	$state = State::getInstance();
	$state -> set('error', 404);
	$state -> set('reason', 'file does not exists');
}

//echo '<pre>';
//echo print_r($router, 1);
//echo '</pre>';

?>