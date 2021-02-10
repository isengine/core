<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Sessions;
use is\Model\Constants\Config;
use is\Model\Data\LocalData;
//use is\Model\Globals\Session;
use is\Model\Components\Session;
use is\Model\Components\Path;
use is\Model\Components\Local;

// читаем сессию

$session = Session::getInstance();
$session -> initialize();

// Подготавливаем конфигурацию

$local = new Local();
$data = new LocalData($local);

// читаем локальные настройки

$config = Config::getInstance();
$type = $config -> get('secure:blockip');

$local -> setFile('ip.' . $type . '.ini');
$data -> joinData($local);

$session -> range = $data -> getData();
$block = $session -> block($type === 'develop' ? 'whitelist' : $type);

if ($block) {
	if ($type === 'develop') {
		echo 'System update. Wait...';
		exit;
		//System::error('update', false, true);
	} else {
		echo 'Your ip in blacklist or not in whitelist';
		exit;
		//System::error('403', false, 'ip in blacklist or not in whitelist');
	}
}

?>