<?php

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\Error;

// читаем uri

$config = Config::getInstance();
$state = State::getInstance();

// инициализация

$error = Error::getInstance();
$error->init($config->get('error:prefix'));
$error->postfix = $config->get('error:postfix');

$find = System::server('request');

$path = $error->path;
$code = Strings::get($find, Strings::find($find, $path) + Strings::len($path), 3);
$path .= $code . $error->postfix;

$next = Strings::get($find, Strings::find($find, $path) + Strings::len($path), 1);

// error из пути, согласно заданным настройкам

if (
    $path
    && System::type($code, 'numeric')
    && Strings::match($find, $path)
    && Objects::match(['', '/', '?', '&'], $next)
) {
    $state->set('error', $code);
}

//echo '[' . Strings::find($find, $path) . ']<br>';
//echo '[' . Strings::match($find, $path) . ']<br>';
//echo '[' . print_r($state, 1) . ']<br>';
//echo '[' . $find . ']<br>';
//echo '[' . $path . ']<br>';
//echo '[' . $code . ']<br>';
//exit;

// error из первого параметра get, согласно общепринятой совместимости с настройками из файла htaccess

if (
    !empty($_GET['error'])
    && System::type($_GET['error'], 'numeric')
    && Objects::first($_GET, 'key') === 'error'
) {
    $state->set('error', $_GET['error']);
}

unset($path, $find, $code);
