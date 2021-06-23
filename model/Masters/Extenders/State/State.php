<?php

namespace is\Masters\Extenders\State;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Parents\Data;
use is\Components\Config;
use is\Components\Language;
use is\Components\Router;
use is\Components\Uri;

class State extends Data {
	
	public function __construct() {
		
		$router = Router::getInstance();
		$uri = Uri::getInstance();
		$lang = Language::getInstance();
		$config = Config::getInstance();
		
		$entry = System::typeClass($router -> current, 'entry');
		
		$main = $router -> template['name'] === $config -> get('default:template') ? null : $router -> template['name'];
		
		$data = [
			'settings' => $router -> getData(),
			
			'template' => $router -> template['name'],
			'section' => $router -> template['section'],
			'page' => $entry ? $router -> current -> getEntryData('name') : null,
			'parents' => $entry ? $router -> current -> getEntryKey('parents') : null,
			'type' => $entry ? $router -> current -> getEntryKey('type') : null,
			
			'route' => $uri -> route,
			'path' => $uri -> route ? Strings::join($uri -> route, '/') . '/' : null,
			'previous' => $uri -> previous,
			'string' => $uri -> path['string'],
			'real' => $config -> get('path:templates') . $router -> template['name'] . DS,
			
			'url' => $uri -> url,
			'domain' => $uri -> domain,
			'site' => $uri -> host,
			'main' => $uri -> domain . ($main ? $main . '/' : null),
			
			'match' => [
				'home' => !System::typeIterable($uri -> route),
				'main' => !System::typeIterable($uri -> route) || Objects::len($uri -> route) === 1 && Objects::first($uri -> route, 'value') === $main
			],
			
			'mail' => $config -> get('users:email'),
			
			'lang' => $lang -> lang,
			'code' => $lang -> code,
			
			'langs' => [
				'list' => Objects::keys($lang -> settings),
				'default' => $config -> get('default:lang'),
				'codes' => null,
				'others' => null,
				'page' => null,
				'parents' => null,
				'route' => null
			]
		];
		
		// другие преобразования
		
		$codes = $lang -> settings;
		if (System::typeIterable($codes)) {
			foreach ($codes as $key => $item) {
				$data['langs']['codes'][$key] = $item['code'];
				if ($key !== $data['lang'] && $key !== $data['langs']['default']) {
					$data['langs']['others'][$key] = $item['code'];
				}
			}
			unset($item);
		}
		unset($codes);
		
		$data['langs']['page'] = $data['page'] ? $lang -> get('menu:' . $data['page']) : null;
		
		$parents = $data['parents'];
		if (System::typeIterable($parents)) {
			foreach ($parents as $item) {
				$name = $lang -> get('menu:' . $item);
				$data['langs']['parents'][] = $name ? $name : $item;
			}
			unset($item);
		}
		unset($parents);
		
		$route = $data['route'];
		if (System::typeIterable($route)) {
			foreach ($route as $item) {
				$name = $lang -> get('menu:' . $item);
				$data['langs']['route'][] = $name ? $name : $item;
			}
			unset($item);
		}
		unset($route);
		
		$this -> setData($data);
		
		unset(
			$main,
			$router,
			$uri,
			$lang,
			$entry,
			$data
		);
		
	}
	
	// группа работы с определением
	
	public function main() {
		// адрес главной страницы шаблона/раздела
		$url = $this -> get('url');
		$route = Strings::join($this -> get('route'), '/');
		$pos = $route ? Strings::find($url, $route) : null;
		return Strings::get($url, 0, $pos);
	}
	
	public function home() {
		// адрес домашней страницы
		return $this -> get('domain');
	}
	
	public function match($type, $name = null) {
		if ($type === 'page') {
			// проверка на название страницы
			return $this -> get('page') === $name;
		} elseif ($type === 'main') {
			// проверка на главную страницу шаблона/раздела
			return $this -> get('route') ? null : true;
		} elseif ($type === 'home') {
			// проверка на домашнюю страницу
			return $this -> get('home');
		}
	}
	
}

?>