<?php

namespace is\Masters\Extenders\Process;

use is\Helpers\Local;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\System;
use is\Helpers\Prepare;
use is\Helpers\Match;
use is\Helpers\Paths;

use is\Parents\Data;

use is\Components\Language; 

class Process extends Data {
	
	// по $data для ссылок и тегов здесь общее правило такое:
	// пути и ключевые значения
	// затем классы
	// затем альты
	
	public $lang;
	
	public function __construct() {
		$lang = Language::getInstance();
		$this -> lang = $lang -> get('lang');
	}
	
	public function launch($data) {
		$data = $this -> objects($data);
		$type = System::typeOf($data);
		if ($type === 'iterable') {
			foreach ($data as $key => $item) {
				$data[$key] = $this -> launch($item);
			}
		} elseif ($type === 'scalar') {
			$data = $this -> strings($data);
		}
		return $data;
	}
	
	public function launchByData() {
		$this -> data = $this -> launch($this -> data);
	}
	
	public function objects($data) {
		if (
			System::typeIterable($data) &&
			$this -> lang &&
			Objects::match(Objects::keys($data), $this -> lang)
		) {
			$data = $data[$this -> lang];
		}
		return $data;
	}
	
	public function strings($string) {
		return Parser::textVariables($string, function($type, $data){
			$name = __NAMESPACE__ . '\\' . Prepare::upperFirst($type);
			$call = new $name;
			return $call -> launch($data);
		});
	}
	
}

?>