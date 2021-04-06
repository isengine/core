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
use is\View;

// читаем конфиг

$config = Config::getInstance();

// согласно конфигу, мы должны инициализировать шаблонизатор
// пока что будет только системный шаблон
// но в дальнейшем должна быть возможность подключать другие шаблонизаторы

// загружаем последовательность инициализации

$path = __DIR__;

System::includes('base', $path);
System::includes('settings', $path);
System::includes('data', $path);
System::includes('seo', $path);
System::includes('views', $path);
System::includes('launch', $path);

//$print = Display::getInstance();
//$print -> dump($user -> getData());
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($state);
//
//exit;

?>