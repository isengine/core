<?php

namespace is\Model\Views\Layout;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

use is\Model\Parents\Data;
use is\Model\Views\View;

abstract class Master extends Data {
	
	public $path;
	public $cache;
	public $parent;
	public $caching;
	
	public function __construct() {
		$view = View::getInstance();
		$data = $view -> get('state') -> getData();
		$this -> setData($data);
		unset($data, $view);
	}
	
	// здесь установка путей и механизм кэширования
	
	public function getCache($name = null) {
		if (!$this -> cache) {
			if ($name) {
				$this -> setCache($name);
			} else {
				return null;
			}
		}
		return $this -> cache;
	}
	
	public function read($name = null) {
		
		$path = $this -> getCache($name);
		
		if (!$path || !file_exists($path)) {
			return null;
		}
		
		ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
		foreach (Local::readFileGenerator($path) as $line) {
			echo $line;
			$data = ob_get_clean();
			echo $data;
			unset($line, $data);
		}
		ob_end_clean();
		unset($line, $data);
		
		return true;
		
	}
	
	public function write($name) {
		
		ob_start();
		$this -> loadPath($name);
		$data = ob_get_contents();
		ob_end_clean();
		echo $data;
		
		$path = $this -> getCache();
		if (!$path || !$data) {
			return null;
		}
		
		Local::createFile($path);
		Local::writeFile($path, $data, 'replace');
		
	}
	
	public function load($path) {
		if (file_exists($path)) {
			require $path;
		}
	}
	
	// абстрактные методы
	
	abstract public function setCache($name);
	abstract public function parsePath();
	abstract public function setPath($name);
	abstract public function getPath();
	abstract public function loadPath($name);
	
	// общие установки кэша
	
	public function caching($value = 'skip') {
		if ($value !== 'skip') {
			$this -> caching = $value;
		}
	}
	
	// загрузка страниц или блоков
	
	public function includes($name, $cache) {
		$cache_backup = $this -> caching;
		$this -> caching($cache);
		if ($this -> caching) {
			if (!$this -> read($name)) {
				$this -> write($name);
			}
		} else {
			$this -> loadPath($name);
		}
		$this -> caching($cache_backup);
	}
	
	/*
	public function includes($name = null, $from = null, $cache = 'skip') {
		if (!$from) {
			$this -> includePage($name, $cache);
		} elseif ($from === 'block') {
			$this -> includeBlock($name, $cache);
		} else {
			$path = $this -> parent['path'] . 'html' . DS . $from . DS . $this -> parsePagePath($name) . '.php';
			$this -> loadPath($path);
		}
	}
	*/
	
}

?>