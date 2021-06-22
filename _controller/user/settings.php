<?php

// Рабочее пространство имен

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
use is\Masters\Database;

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
	$db -> driver -> filter -> addFilter('type', 'settings');
	$db -> driver -> filter -> addFilter('name', 'default');
	$db -> launch();
	
	$suset = $db -> data -> getFirstData();
	
	$db -> clear();
	
	$session -> setValue('settings', json_encode($suset));
	
}

$user -> setSettings($suset);
unset($suset);

$user -> setSpecial();

?>