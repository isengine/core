<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Url;
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
use is\Model\Components\Language;
use is\Model\Database\Database;
use is\Model\Database\Datasheet;

// читаем api

$config = Config::getInstance();
$state = State::getInstance();
$user = User::getInstance();
$uri = Uri::getInstance();
$session = Session::getInstance();
$lang = Language::getInstance();


echo '<pre>';
//echo print_r($uri, 1);
echo '</pre>';

?>