<?php

// Рабочее пространство имен

namespace is;

use is\Model\Components\Config;
use is\Model\Components\Error;

// инициализация

$config = Config::getInstance();

$error = Error::getInstance();
$error -> init($config -> get('error:url'));
$error -> prefix = $config -> get('error:prefix');
$error -> postfix = $config -> get('error:postfix');

//use is\Model\Components\Error;
//$error = Error::getInstance();
//$error -> code = 404;
//$error -> reload();

?>