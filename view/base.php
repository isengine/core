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

$settings = [
	'view' => $config -> get('default:view'),
	'path' => $config -> get('path:templates') . $router -> template['name'] . DS,
	'cache' => $config -> get('path:cache') . 'templates' . DS
];

$template = Template::getInstance();
$template -> init($settings);
$template -> launch();

//echo 'title::' . $template -> lang('title') . '<br>';
//echo 'info::' . $template -> lang('information') . '<br>';
//echo 'info-ver::' . $template -> lang('information:version') . '<br>';
//echo 'info-ph-0::' . $template -> lang('information:phone:0') . '<br>';
//echo print_r($template -> getRealPath(), 1) . '<br>';

//echo print_r($template -> view -> getPagePath(), 1) . '<br>';
//echo print_r($template -> view -> getCachePage(), 1) . '<br>';
//echo print_r($template -> view -> getBlockPath('home'), 1) . '<br>';
echo print_r($template -> view -> setCachePages(true), 1) . '<br>';
echo print_r($template -> view -> include('home'), 1) . '<br>';

$print = Display::getInstance();
//$print -> dump($user -> getData());
//echo '<hr>';
//$print -> dump($db);
//$print -> dump($uri);
//$print -> dump($state);
//$print -> dump($template);
//$print -> dump($router);
//
//exit;

?>