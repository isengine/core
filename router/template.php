<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Sessions;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Model\Components\Session;
use is\Model\Components\Uri;
use is\Model\Components\State;
use is\Model\Components\Config;
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

// читаем массив шаблонов

$templates = Local::list($config -> get('path:templates'), ['return' => 'folders']);
foreach ($templates['folders'] as $item) {
	$templates['array'][] = $item['name'];
}
unset($item);
$templates = $templates['array'];

// задаем массив возможных значений шаблона по приоритету

$array = [
	'section' => $state -> get('section'),
	'error' => $state -> get('error') ? $config -> get('url:error:template') : null,
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

if (System::typeIterable($router -> route)) {
	$template = Objects::first($router -> route, 'value');
	if (Objects::match($templates, $template)) {
		$array['route'] = $template;
	}
	unset($template);
}

// проверяем секцию

if ($array['section'] && $array['error']) {
	
	if (!Objects::match($templates, $array['section'])) {
		$array['section'] = null;
	} else {
		
		$templates = Local::list($config -> get('path:templates') . $array['section'] . DS . 'sections' . DS, ['return' => 'folders']);
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

if ($router -> template['name'] === $array['route']) {
	$router -> route = Objects::unfirst($router -> route);
}

unset($array, $templates);

//echo '<pre>';
//echo print_r($router, 1);
//echo '</pre>';

?>