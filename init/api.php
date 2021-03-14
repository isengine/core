<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Model\Components\Error;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Components\Language;
use is\Model\Components\Path;
use is\Model\Components\Api;
use is\Model\Databases\Database;
use is\Model\Databases\Datasheet;

// читаем api

$api = Api::getInstance();
$error = Error::getInstance();
$config = Config::getInstance();
$state = State::getInstance();
$user = User::getInstance();
$uri = Uri::getInstance();
$session = Session::getInstance();
$lang = Language::getInstance();

//$dbset = $config -> getArray('db', true);
//$db = new Datasheet;
//$db -> init($dbset);
//$db -> query('read');
//$db -> rights(true);
//$db -> collection('languages');
//$db -> driver -> addFilter('type', 'settings');
//$db -> driver -> addFilter('name', 'default');
//$db -> launch();
//$a = $db -> data -> getFirstData();
//$db -> clear();

//$a = $state -> get('error');
//$a = $state -> get('api');

//echo '<pre>';
//echo print_r($a, 1) . '<br>';
//echo print_r($api, 1);
//echo '</pre>';

?>