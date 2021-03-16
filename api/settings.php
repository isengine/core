<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Apis\Api;
use is\Model\Databases\Database;

// читаем user

$api = Api::getInstance();
$user = User::getInstance();
$session = Session::getInstance();

// загружаем установки апи из базы данных

$db = Database::getInstance();
$db -> collection('api');
$db -> driver -> addFilter('parents', $api -> class);
$db -> driver -> addFilter('name', $api -> method);
$db -> driver -> addFilter('type', '-settings');
$db -> launch();

$api -> setSettings( $db -> data -> getFirstData() );

$db -> clear();

//$us = $user

//echo '<pre>';
//echo print_r($api, 1);
//echo print_r($user, 1);
//echo '</pre>';

?>