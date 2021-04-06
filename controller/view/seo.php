<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Databases\Database;
use is\Model\Templates\Template;

// читаем

$template = Template::getInstance();

$db = Database::getInstance();

$db -> collection('seo');
$db -> driver -> filter -> addFilter('name', 'default');
$db -> driver -> filter -> addFilter('type', 'settings');
$db -> launch();

$template -> seo -> setData( $db -> data -> getFirstData() );

$db -> clear();

$page = $template -> get('page');

if ($page) {
	$db -> driver -> filter -> addFilter('name', $page);
	$db -> launch();
	
	$template -> seo -> mergeData( $db -> data -> getFirstData() );
	
	$db -> clear();
}

//echo '<pre>';
//echo print_r($template -> seo -> getData(), 1);
//echo print_r($template -> view -> getData(), 1);
//echo '</pre>';

?>