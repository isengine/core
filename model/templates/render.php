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

class Render {
	
	public $from;
	public $to;
	public $mtime;
	public $hash;
	
	// инициализация
	
	public function init($from, $to) {
		$this -> from = $from;
		$this -> to = $to;
		$this -> mtime = file_exists($this -> from) ? filemtime($this -> from) : null;
	}
	
	//public function renderLink($type, $subtype = null) {
	//	// создание ссылки
	//	$result = null;
	//	if ($type === 'style') {
	//		$result = '<link rel="stylesheet' . ($subtype ? '/' . $subtype : null) . '" rev="stylesheet" type="text/css" href="' . $this -> to . '" />';
	//	} elseif ($type === 'script') {
	//		$result = '<script type="text/javascript" src="' . $this -> to . '"></script>';
	//	}
	//	return $result;
	//}
	
	public function modify() {
		// создание модификатора по времени последнего изменения
		// если вы используете хэш, то модификатор добавляете в самом конце, после расчета хэша
		if ($this -> mtime) {
			$this -> to .= '?' . $this -> mtime;
		}
	}
	
	public function reset() {
		// сброс настроек рендеринга
		$this -> from = null;
		$this -> to = null;
		$this -> mtime = null;
		$this -> hash = [];
	}
	
	public function setHash() {
		
		// создание md5 хэша по времени последнего изменения
		
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
		$from = getHash('from');
		$to = getHash('to');
		return $from && $to && $from === $to;
	}
	
	public function writeHash() {
		// запись md5 хэша в файл
		$file = getHash('file');
		if (!file_exists($file)) {
			Local::createFile($file);
		}
		Local::writeFile($file, getHash('from'), 'replace');
	}
	
	// use:
	// init(from..., to...);
	// setHash();
	// if (!matchHash()) {
	//   if (some-function-with-rendering-and-return-result-rendering-process...()) {
	//     writeHash();
	//   }
	// }
	// modify();
	// some-function-with-print-result-rendering-link...()
	
}

?>