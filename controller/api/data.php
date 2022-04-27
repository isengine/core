<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Masters\Api;
use is\Masters\Database;

// читаем user

$api = Api::getInstance();
$user = User::getInstance();
$session = Session::getInstance();

// формируем обработчик апи из настроек

// Здесь речь о том, что есть разные условия запуска методов
// например, время сессии, количество попыток, вообще разрешен ли метод по ключу апи
// разрешен ли метод без ключа апи, обязательна ли авторизация пользователя
// и еще целая куча разных подобных данных
// если все ок, то продолжаем

// при этом важно помнить, что независимо от настройки server в api
// сам url api в строке должен быть вызван

//echo '<pre>';
//echo print_r($api, 1);
//echo print_r($user, 1);
//echo '</pre>';
