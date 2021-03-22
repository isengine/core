<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
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
use is\Model\Databases\Database;

// читаем user

//$uri = Uri::getInstance();
//$user = User::getInstance();
//$session = Session::getInstance();

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();

// задаем массив возможных значений шаблона по приоритету

$array = [
	'error' => $state -> get('error') ? $config -> get('url:error:template') : null,
	'settings' => $router -> current -> data['template'],
	'parents' => null,
	'route' => null,
	'default' => $config -> get('default:template')
];

// проверяем шаблон из родителей

$parents = [];
$p = null;
foreach ($router -> current -> parents as $item) {
	$p .= ($p ? ':' : null) . $item;
	$parents[] = $p;
}
unset($item);
unset($p);
$parents = Objects::reverse($parents);
foreach ($parents as $item) {
	$data = $router -> structure -> getDataByName($item);
	if ($data['template']) {
		$array['parents'] = $data['template'];
		break;
	}
}
unset($item);
unset($parents);

// проверяем шаблон из первого урла

$template = Objects::first($router -> route, 'value');
$t = Local::list($config -> get('path:templates'), ['return' => 'folders']);
foreach ($t['folders'] as $item) {
	if ($item['name'] === $template) {
		$array['route'] = $template;
		break;
	}
}
unset($item);
unset($t, $template);

// устанавливаем шаблон

$router -> template = Objects::first( Objects::clear($array), 'value' );

unset($array);

//echo '<pre>';
//echo print_r($router, 1);
//echo '</pre>';

?>