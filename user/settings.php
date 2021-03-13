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

// проверяем сессию на наличие настроек полей пользователя

$suset = $session -> getValue('settings');

if ($suset) {
	
	$suset = json_decode($suset, true);
	
} else {
	
	$db = Database::getInstance();
	$db -> collection('users');
	$db -> driver -> addFilter('type', 'settings');
	$db -> driver -> addFilter('name', 'default');
	$db -> launch();
	
	$suset = $db -> data -> getFirstData();
	
	$db -> clear();
	
	$session -> setValue('settings', json_encode($suset));
	
}

$user -> setSettings($suset);
unset($suset);

$user -> setSpecial();

?>