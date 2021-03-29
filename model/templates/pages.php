<?php

namespace is\Model\Templates;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

class Pages {
	
	public $caching_blocks;
	public $caching_pages;
	
	public $cache_block;
	public $path_block;
	public $cache_page;
	public $path_page;
	
	// кэширование страниц
	
	public function setCachePage($name = null) {
		
		$section = $this -> section();
		
		$path = $this -> cache . 'pages' . DS . $this -> template() . DS . ($section ? 'section' . DS . $section . DS : null);
		
		if ($name) {
			$path_route = $this -> parsePagePath($name);
		} else {
			$route = $this -> route();
			$path_route = System::typeIterable($route) ? Strings::join($route, DS) : 'index';
		}
		
		$this -> cache_page = $path . (!file_exists($path . $path_route) ? 'index' : $path_route) . '.php';
		
	}
	
	public function getCachePage($name = null) {
		if (!$this -> cache_page) {
			$this -> setCachePage($name);
		}
		return $this -> cache_page;
	}
	
	public function readCachePage($name = null) {
		$path = $this -> getCachePage($name);
		return $this -> openCache($path);
	}
	
	public function writeCachePage($data = null) {
		$path = $this -> getCachePage();
		$this -> writeCache($path, $data);
	}
	
	public function clearCachePage($name = null) {
		Local::eraseFolder($this -> getCachePage($name));
	}
	
	// кэширование блоков
	
	public function setCacheBlock($name) {
		$array = $this -> parseBlockPath($name);
		$this -> cache_block = $this -> cache . 'blocks' . DS . $array[0] . DS . $array[1] . '.php';
	}
	
	public function getCacheBlock($name = null) {
		if (!$this -> cache_block) {
			if ($name) {
				$this -> setCacheBlock($name);
			} else {
				return null;
			}
		}
		return $this -> cache_block;
	}
	
	public function readCacheBlock($name = null) {
		$path = $this -> getCacheBlock($name);
		return $this -> openCache($path);
	}
	
	public function writeCacheBlock($data = null) {
		if (!$this -> cache_block) {
			return null;
		}
		$path = $this -> getCacheBlock();
		$this -> writeCache($path, $data);
	}
	
	public function clearCacheBlock($name = null) {
		Local::eraseFolder($this -> getCacheBlock($name));
	}
	
	// общие установки кэша
	
	public function openCache($path = null) {
		
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
	
	public function writeCache($path = null, $data = null) {
		if (!$path || !$data) {
			return null;
		}
		Local::createFile($path);
		Local::writeFile($path, $data, 'replace');
	}
	
	public function setCachePages($value = 'skip') {
		if ($value !== 'skip') {
			$this -> caching_pages = $value;
		}
	}
	
	public function setCacheBlocks($value = 'skip') {
		if ($value !== 'skip') {
			$this -> caching_blocks = $value;
		}
	}
	
	// общая очистка кэша
	
	public function clearCachePages() {
		Local::eraseFolder($this -> cache . 'pages' . DS);
	}
	
	public function clearCacheBlocks() {
		Local::eraseFolder($this -> cache . 'blocks' . DS);
	}
	
	public function clearCache() {
		Local::eraseFolder($this -> cache);
	}
	
	// адрес пути страницы
	
	public function parsePagePath($name = null) {
		if (!$name) {
			return null;
		}
		$array = Parser::fromString($name);
		return Strings::join($array, DS);
	}
	
	public function setPagePath() {
		
		$section = $this -> section();
		$route = $this -> route();
		
		$path = $this -> path . ($section ? 'section' . DS . $section . DS : 'inner' . DS);
		
		$path_index = 'index.php';
		$path_route = System::typeIterable($route) ? Strings::join($route, DS) . '.php' : $path_index;
		
		$this -> path_page = $path . (!file_exists($path . $path_route) ? $path_index : $path_route);
		
	}
	
	public function getPagePath() {
		
		if (!$this -> path_page) {
			$this -> setPagePath();
		}
		
		return $this -> path_page;
		
	}
	
	// адрес пути блока
	
	public function parseBlockPath($name = null) {
		if (!$name) {
			return null;
		}
		$array = Parser::fromString($name);
		if (!$array[1]) {
			$array[1] = $array[0];
			$array[0] = $this -> template();
		}
		return $array;
	}
	
	public function setBlockPath($name) {
		
		$array = $this -> parseBlockPath($name);
		$parent = Paths::parent($this -> path);
		$this -> path_block = $parent . $array[0] . DS . 'blocks' . DS . $array[1] . '.php';
		
	}
	
	public function getBlockPath($name = null) {
		
		if ($name) {
			$this -> setBlockPath($name);
		} elseif (!$this -> path_block) {
			return null;
		}
		return $this -> path_block;
		
	}
	
	// загрузка страниц или блоков
	
	private function loadPath($path) {
		//echo $path;
		if (file_exists($path)) {
			require $path;
		}
	}
	
	public function loadBlock($name) {
		$path = $this -> getBlockPath($name);
		echo '[' . $path . ']';
		$this -> loadPath($path);
	}
	
	public function loadPage($name) {
		if (!$name) {
			$path = $this -> getPagePath();
		} else {
			$path = $this -> path . 'inner' . DS . $this -> parsePagePath($name) . '.php';
		}
		$this -> loadPath($path);
	}
	
	public function includeBlock($name, $cache) {
		$this -> setCacheBlocks($cache);
		if ($this -> caching_blocks) {
			$cache = $this -> readCacheBlock($name);
			if (!$cache) {
				ob_start();
				$this -> loadBlock($name);
				$cache_data = ob_get_contents();
				ob_end_clean();
				echo $cache_data;
				$this -> writeCacheBlock($cache_data);
			}
		} else {
			$this -> loadBlock($name);
		}
	}
	
	public function includePage($name, $cache) {
		$this -> setCachePages($cache);
		if ($this -> caching_pages) {
			$cache = $this -> readCachePage($name);
			if (!$cache) {
				ob_start();
				$this -> loadPage($name);
				$cache_data = ob_get_contents();
				ob_end_clean();
				echo $cache_data;
				$this -> writeCachePage($cache_data);
			}
		} else {
			$this -> loadPage($name);
		}
	}
	
	public function includes($name = null, $from = null, $cache = 'skip') {
		if (!$from) {
			$this -> includePage($name, $cache);
		} elseif ($from === 'block') {
			$this -> includeBlock($name, $cache);
		} else {
			$path = $this -> path . $from . DS . $this -> parsePagePath($name) . '.php';
			$this -> loadPath($path);
		}
	}
	
}

?>