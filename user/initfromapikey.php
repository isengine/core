<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Helpers\Match;
use is\Model\Components\Path;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Uri;
use is\Model\Components\User;
use is\Model\Components\Session;
use is\Model\Databases\Database;
use is\Model\Apis\Api;

// инициализация

$config = Config::getInstance();
$user = User::getInstance();
$api = Api::getInstance();
$session = Session::getInstance();

$field = $user -> getFieldsNameBySpecial('apikey');
$ukey = $api -> key['password'];
$uname = $api -> key['name'];

//{"name":"common","password":"password"}
//eyJuYW1lIjoiY29tbW9uIiwicGFzc3dvcmQiOiJwYXNzd29yZCJ9

$db = Database::getInstance();
$db -> collection('users');
$db -> driver -> addFilter('data:' . $field, $key);
$db -> launch();

$apiuser = json_encode( $db -> data -> getFirst() );

$db -> clear();

if ($apiuser) {
	$session -> setValue('user', $apiuser);
}

//echo '[' . print_r($field, 1) . ']<br>';
//echo '[' . print_r($key, 1) . ']<br>';
//echo '[' . print_r($_SESSION, 1) . ']<br>';

//echo '<pre>';
//echo print_r($session, 1);
//echo print_r($user, 1);
//echo print_r($api, 1);
//echo '</pre>';

//$session -> setValue('user', );

?>