<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Components\Router;
use is\Model\Databases\Database;

// читаем user

//$uri = Uri::getInstance();
//$user = User::getInstance();
//$session = Session::getInstance();

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();

$db = Database::getInstance();

$db -> collection('templates');
$db -> driver -> filter -> addFilter('name', $router -> template['name']);
$db -> launch();

$data = $db -> data -> getFirstData();

if ($data) {
	$router -> setData( $data );
	$router -> addData('mtime', $db -> data -> getFirst() -> getEntryKey('mtime') );
}

$db -> clear();

//echo '<pre>';
//echo print_r($router -> getData(), 1);
//echo '</pre>';

?>