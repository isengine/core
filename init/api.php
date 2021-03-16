<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Helpers\Match;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\Uri;
use is\Model\Components\Session;
use is\Model\Components\User;
use is\Model\Apis\Api;

// инициализация

$config = Config::getInstance();
$state = State::getInstance();
$uri = Uri::getInstance();
$user = User::getInstance();

echo '<pre>';
echo print_r($state, 1);
echo print_r($user, 1);
echo '</pre>';

$session = Session::getInstance();

if (
	$uri -> getPathArray(0) &&
	$config -> get('url:api:name') &&
	$uri -> getPathArray(0) === $config -> get('url:api:name')
) {
	$state -> set('api', true);
} else {
	$state -> set('api', false);
}

if (!$state -> get('api')) {
	return;
}

// инициализируем апи с параметрами

$apiset = [
	'class' => $uri -> getPathArray(1),
	'method' => $uri -> getPathArray(2),
	'key' => $uri -> getData( $config -> get('url:api:key') ),
	'token' => $uri -> getData( $config -> get('url:api:token') ),
	'data' => $uri -> getData()
];

unset($apiset['data'][ $config -> get('url:api:key') ]);

$api = new Api($apiset);

echo print_r($api, 1);

//$str = '{"user":"user","password":"password"}';
//echo '[' . Prepare::encode($str) . ']';
//$str = 'eyJ1c2VyIjoidXNlciIsInBhc3N3b3JkIjoicGFzc3dvcmQifSAg';
//$str = time();
//echo '[' . Prepare::encode($str) . ']';
//$str = 'MTYxNTgyODM0NSAg';

//{
//	"user":"",
//	"password":""
//}

// Дальше нужно делать проверку ключей
// чтение из базы данных разрешений
// подгружать нужный метод, как драйвер базы данных
// который определен через абстрактный интерфейс
// и запускать его

// метод может работать с любыми параметрами системы
// доступ к данным осуществляется из обращения к данным родительского класса

//use is\Model\Components\Api;
//$api = Api::getInstance();
//$api -> name = 404;
//$api -> reload();

?>