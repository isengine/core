<?php

namespace is\Masters\Extenders\Lang;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Local;
use is\Parents\Data;
use is\Components\Language;

class Lang extends Data {
	
	public function __construct() {
		//$lang = Language::getInstance();
		//$this -> setData($lang -> getData());
		// возможно, имеет смысл сделать this -> data ссылкой на язык,
		// чтобы инициализировать компонент языка только один раз
		// также это упростит работу с языком - всякие get/set-Data
		// но надо смотреть, сравнивать по затрате ресурсов
	}
	
	public function get($data = null, $prepare = null) {
		
		if (!System::set($data)) { return null; }
		
		if (!$prepare) {
			return Language::getInstance() -> get($data);
		}
		
		$lang = Language::getInstance() -> get($data);
		Objects::each(Parser::fromString($prepare), function($item) use (&$lang){
			$lang = Prepare::$item($lang);
		});
		
		return $lang;
		
	}
	
	public function getData($null = null) {
		return Language::getInstance() -> getData();
	}
	
	public function setData($data, $null = null) {
		return Language::getInstance() -> setData($data);
	}
	
	public function add($name, $path) {
		$data = Local::readFile($path);
		$data = Parser::fromJson($data);
		if ($data) {
			Language::getInstance() -> addData($name, $data);
		}
	}
	
}

//class Lang {
//	__construct() {
//	}
//}

?>