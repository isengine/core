<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\Config;
use is\Model\Components\State;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Databases\Database;
use is\Model\Templates\Template;

// читаем

$template = Template::getInstance();
$state = State::getInstance();
$config = Config::getInstance();

System::includes(
	'html:template',
	$config -> get('path:templates') . $template -> view -> template()
);

//echo '<pre>';
//echo print_r($path, 1);
//echo '</pre>';

?>