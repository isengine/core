<?php

namespace is\Model\Views\State;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Model\Parents\Data;
use is\Model\Components\Config;
use is\Model\Components\Language;
use is\Model\Components\Router;
use is\Model\Components\Uri;

class State extends Data {
	
	public function __construct() {
		
		$router = Router::getInstance();
		$uri = Uri::getInstance();
		$lang = Language::getInstance();
		$config = Config::getInstance();
		
		$entry = System::typeClass($router -> current, 'entry');
		
		$data = [
			'template' => $router -> template['name'],
			'section' => $router -> template['section'],
			'page' => $entry ? $router -> current -> getEntryData('name') : null,
			'parents' => $entry ? $router -> current -> getEntryKey('parents') : null,
			'type' => $entry ? $router -> current -> getEntryKey('type') : null,
			
			'route' => $router -> route,
			'path' => $router -> route ? Strings::join($router -> route, '/') . '/' : null,
			
			'url' => $uri -> url,
			'domain' => $uri -> domain,
			'home' => !System::typeIterable($router -> route),
			
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
		
		// если последний объект - файл и есть правила роутинга, меняем путь
		
		if ($config -> get('router:folders:convert')) {
			
			$ext = $config -> get('router:folders:extension');
			$idx = $config -> get('router:folders:index');
			
			$ext = $ext ? '.' . $ext : '.php';
			$idx = $idx ? ($config -> get('router:index') ? $config -> get('router:index') : 'index') . $ext : null;
			
			if ($ext && System::set($uri -> file)) {
				$last = Objects::last($uri -> path['array'], 'value');
				if ($idx === $last || !System::set($data['path'])) {
					$data['path'] .= $idx;
				} else {
					$data['path'] = Strings::unlast($data['path']);
					$data['path'] .= $ext;
				}
			}
			
			unset($ext, $idx);
			
		}
		
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