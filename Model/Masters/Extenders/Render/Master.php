<?php

namespace is\Model\Masters\Extenders\Render;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

use is\Model\Parents\Data;

abstract class Master extends Data {
	
	public $from;
	public $to;
	public $url;
	public $mtime;
	public $hash;
	
	// инициализация
	
	public function __construct($from, $to, $url) {
		
		// задаем базовые настройки путей
		// каждый из них записан в виде массива с двумя индексами
		// 0 - указывает на начало пути
		// 1 - на конец пути
		// задав их всего один раз, дальше можно будет использовать в качестве оболочки
		
		// from - real путь, где лежит исходний файл
		// to - real путь, куда будет скомпилирован файл
		// url - url-путь, абсолютный или относительный, для ссылки на файл
		
		$this -> from = $from;
		$this -> to = $to;
		$this -> url = $url;
		
		//$this -> mtime = file_exists($this -> from) ? filemtime($this -> from) : null;
		
	}
	
	abstract public function launch($null);
	
	public function setPath($middle = null, $last = null) {
		foreach (['from', 'to', 'url'] as $item) {
			$name = $this -> $item;
			$this -> $item = $name[0] . $middle . $name[1] . $last;
		}
		unset($name, $item);
	}
	
	public function setPathByKey($key, $middle = null, $last = null) {
		$name = $this -> $key;
		$this -> $key = $name[0] . $middle . $name[1] . $last;
	}
	
	public function getPathByKey($key, $middle = null, $last = null) {
		$name = $this -> $key;
		return $name[0] . $middle . $name[1] . $last;
	}
	
	public function modificator() {
		// создание модификатора по времени последнего изменения
		// если вы используете хэш, то модификатор добавляете в самом конце, после расчета хэша
		if ($this -> mtime) {
			return '?' . $this -> mtime;
		}
	}
	
	public function reset() {
		// сброс настроек рендеринга
		$this -> from = null;
		$this -> to = null;
		$this -> url = null;
		$this -> mtime = null;
		$this -> hash = [];
	}
	
	public function write($data) {
		// запись отрендеренных данных
		if (!file_exists($this -> to)) {
			Local::createFile($this -> to);
		}
		Local::writeFile($this -> to, $data, 'replace');
	}
	
	public function setMtime() {
		$this -> mtime = file_exists($this -> from) ? filemtime($this -> from) : null;
	}
	
	public function setHash() {
		// создание md5 хэша по времени последнего изменения
		if (!$this -> mtime) {
			$this -> setMtime();
		}
		$this -> hash = [
			'file' => $this -> to . '.md5',
			'from' => null,
			'to' => null
		];
		$this -> hash['to'] = file_exists($this -> hash['file']) ? file_get_contents($this -> hash['file']) : null;
		$this -> hash['from'] = $this -> mtime ? md5_file($this -> from) . md5($this -> mtime) : null;
	}
	
	public function getHash($name = null) {
		// возвращение значение md5 хэша
		return $name ? $this -> hash[$name] : $this -> hash;
	}
	
	public function matchHash() {
		// проверка значений md5 хэша
		$from = $this -> getHash('from');
		$to = $this -> getHash('to');
		return $from && $to && $from === $to;
	}
	
	public function writeHash() {
		// запись md5 хэша в файл
		$file = $this -> getHash('file');
		if (!file_exists($file)) {
			Local::createFile($file);
		}
		Local::writeFile($file, $this -> getHash('from'), 'replace');
	}
	
}

?>