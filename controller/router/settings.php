<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Parser;
use is\Helpers\Local;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Components\Router;
use is\Masters\Database;

// читаем user

//$uri = Uri::getInstance();
//$user = User::getInstance();
//$session = Session::getInstance();

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();

// читаем данные из настроек шаблона

$path = $config -> get('path:templates') . $router -> template['name'] . DS . 'settings.ini';

$local = Local::readFile($path);
$local = Parser::fromJson($local);

$router -> setData( $local ? $local : null );
$router -> addData('mtime', $local ? filemtime($path) : time() );

// читаем настройки шаблона из базы

$db = Database::getInstance();

$db -> collection('templates');
$db -> driver -> filter -> addFilter('name', $router -> template['name']);
$db -> launch();

$data = $db -> data -> getFirstData();

if ($data) {
	$router -> mergeData( $data, true );
	$router -> addData('mtime', $db -> data -> getFirst() -> getEntryKey('mtime') );
}

$db -> clear();

//echo '<pre>';
//echo print_r($router -> getData(), 1);
//echo '</pre>';

?>