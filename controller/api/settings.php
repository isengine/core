<?php

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
use is\Masters\Api;
use is\Masters\Database;

// читаем user

$api = Api::getInstance();
$user = User::getInstance();
$session = Session::getInstance();

// загружаем установки апи из базы данных

$db = Database::getInstance();
$db->collection('api');
$db->driver->filter->addFilter('parents', $api->class);
$db->driver->filter->addFilter('name', $api->method);
$db->driver->filter->addFilter('type', '-settings');
$db->launch();

$api->setSettings($db->data->getFirstData());

$db->clear();

//$us = $user

//echo '<pre>';
//echo print_r($api, 1);
//echo print_r($user, 1);
//echo '</pre>';
