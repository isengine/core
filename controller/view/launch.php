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
use is\Model\Components\Router;
use is\Model\Databases\Database;

// читаем

$state = State::getInstance();
$config = Config::getInstance();
$router = Router::getInstance();

$section = $state -> get('section') ? 'sections:' . $router -> template['section'] . ':' : null;

System::includes(
	'html:' . $section . 'template',
	$config -> get('path:templates') . $router -> template['name']
);

//echo '<pre>';
//echo print_r($path, 1);
//echo '</pre>';

?>