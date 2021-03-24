<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Math;
use is\Model\Components\Path;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
use is\Model\Components\Content;
use is\Model\Components\Display;
use is\Model\Components\Log;
use is\Model\Components\User;
use is\Model\Components\Router;
use is\Model\Templates\Template;

// читаем конфиг

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();
$uri = Uri::getInstance();

// согласно конфигу, мы должны инициализировать шаблонизатор
// пока что будет только системный шаблон
// но в дальнейшем должна быть возможность подключать другие шаблонизаторы

// инициализируем шаблонизатор с параметрами

//$tset = [
//	'name' => $router -> getPathArray(1),
//	'method' => $uri -> getPathArray(2),
//	'key' => $uri -> getData( $config -> get('url:api:key') ),
//	'token' => $uri -> getData( $config -> get('url:api:token') ),
//	'data' => $uri -> getData()
//];

$view = $config -> get('default:view');
$path = $config -> get('path:templates') . $router -> template['name'] . DS;

$template = Template::getInstance();
$template -> init($view, $path);

//echo 'title::' . $template -> lang('title') . '<br>';
//echo 'info::' . $template -> lang('information') . '<br>';
//echo 'info-ver::' . $template -> lang('information:version') . '<br>';
//echo 'info-ph-0::' . $template -> lang('information:phone:0') . '<br>';

echo print_r($p, 1) . '<br>';
echo print_r($template -> getRealPath(), 1) . '<br>';

$print = Display::getInstance();
//$print -> dump($user -> getData());
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($state);
//$print -> dump($template);
$print -> dump($router);
//
//exit;

?>