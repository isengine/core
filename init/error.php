<?php

// Рабочее пространство имен

namespace is;

use is\Model\Components\Config;
use is\Model\Components\Error;

// инициализация

$config = Config::getInstance();

$error = Error::getInstance();
$error -> init($config -> get('url:error:url'));
$error -> prefix = $config -> get('url:error:prefix');
$error -> postfix = $config -> get('url:error:postfix');

//use is\Model\Components\Error;
//$error = Error::getInstance();
//$error -> code = 404;
//$error -> reload();

?>