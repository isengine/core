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
use is\Model\Databases\Database;

// читаем user

$user = User::getInstance();
$session = Session::getInstance();
$state = State::getInstance();

// подгружаем данные из БД

$db = Database::getInstance();
$db -> collection('structures');
$db -> driver -> format('structure');
//$db -> driver -> addFilter('name', $uname);
//$db -> driver -> addFilter('data:' . $field, $ukey);
$db -> launch();

$structure = $db -> data -> getData();

$db -> clear();

echo '<pre>';
echo print_r($structure, 1);
echo '</pre>';

?>