<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Databases\Database;
use is\Model\Templates\Template;

// читаем

$template = Template::getInstance();
$config = Config::getInstance();

$db = Database::getInstance();

$db -> collection('templates');
$db -> driver -> filter -> addFilter('name', $template -> get('template'));
$db -> launch();

$template -> settings -> setData( $db -> data -> getFirstData() );

$db -> clear();

//echo '<pre>';
//echo print_r($template -> getData(), 1);
//echo '</pre>';

?>