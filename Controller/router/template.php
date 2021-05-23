<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Components\Session;
use is\Components\Uri;
use is\Components\State;
use is\Components\Config;
use is\Components\Display;
use is\Components\Log;
use is\Components\User;
use is\Components\Router;
use is\Masters\Database;

// читаем user

$uri = Uri::getInstance();
//$user = User::getInstance();
//$session = Session::getInstance();

$config = Config::getInstance();
$state = State::getInstance();
$router = Router::getInstance();

// читаем массив шаблонов

$templates = Local::search($config -> get('path:templates'), ['return' => 'folders']);
foreach ($templates['folders'] as $item) {
	$templates['array'][] = $item['name'];
}
unset($item);
$templates = $templates['array'];

// задаем массив возможных значений шаблона по приоритету

$array = [
	'section' => $state -> get('section'),
	'error' => $state -> get('error') ? $config -> get('error:template') : null,
	'settings' => $router -> current -> data['template'],
	'parents' => null,
	'route' => null,
	'default' => $config -> get('default:template')
];

// проверяем шаблон из родителей

if (System::typeIterable($router -> current -> parents)) {
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
}

// проверяем шаблон из первого урла

$route = $uri -> getRoute();
if (System::typeIterable($route)) {
	$template = Objects::first($route, 'value');
	if (Objects::match($templates, $template)) {
		$array['route'] = $template;
	}
	unset($template);
}
unset($route);

// проверяем секцию

if ($array['section'] && $array['error']) {
	
	if (!Objects::match($templates, $array['section'])) {
		$array['section'] = null;
	} else {
		
		$templates = Local::search($config -> get('path:templates') . $array['section'] . DS . 'html' . DS . 'sections' . DS, ['return' => 'folders']);
		
		if (System::typeIterable($templates)) {
			foreach ($templates['folders'] as $item) {
				$templates['array'][] = $item['name'];
			}
			unset($item);
			$templates = $templates['array'];
		} else {
			$templates = [];
		}
		
		if (Objects::match($templates, 'default')) {
			$router -> template['section'] = 'default';
			//$array['section'] .= ':default';
		} else {
			$array['section'] = null;
		}
		
	}
	
}

// устанавливаем шаблон

//echo '<pre>';
//echo print_r($templates, 1);
//echo print_r($array, 1);
//echo '</pre>';

$router -> template['name'] = Objects::first( Objects::clear($array), 'value' );

//if ($router -> template['name'] === $array['route']) {
//	$uri -> unRoute('first');
//}

unset($array, $templates);

//echo '<pre>';
//echo print_r($router, 1);
//echo '</pre>';

?>