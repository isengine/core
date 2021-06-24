<?php

namespace is\Masters\Extenders\Translit;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Local;
use is\Parents\Data;
use is\Components\Language;

class Translit extends Data {
	
	public $lang;
	
	public function __construct() {
		$lang = Language::getInstance();
		$this -> lang = $lang -> get('lang');
		$this -> setData($lang -> getData('translit'));
	}
	
	public function launch($string, $to = null, $from = null) {
		
		if (!$string) {
			return null;
		}
		if (
			(!$to && !$from) ||
			($to === $from)
		) {
			return $string;
		}
		
		if (!$to) {
			$to = $this -> lang;
		}
		if (!$from) {
			$from = $this -> lang;
		}
		
		foreach ($this -> getData() as $key => $item) {
			
			if ($from === $this -> lang) {
				$needle = $key;
				$haystack = $item[$to];
			} else {
				$needle = $item[$from];
				$haystack = $key;
			}
			
			if (System::type($haystack, 'array')) {
				$haystack = Objects::last($haystack, 'value');
			}
			
			$string = Strings::replace($string, $needle, $haystack);
			
		}
		unset($key, $item);
		
		return $string;
		
	}
	
}

?>