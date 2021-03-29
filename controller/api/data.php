<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Apis\Api;
use is\Model\Databases\Database;

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

//echo '<pre>';
//echo print_r($api, 1);
//echo print_r($user, 1);
//echo '</pre>';

?>