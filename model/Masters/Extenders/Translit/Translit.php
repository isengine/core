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
use is\Components\Cache;
use is\Components\Config;
use is\Components\Language;
use is\Masters\Database;

/*
* С транслитом вообще штука сложная
* Когда указывается язык 'from',
* Он не только определялся, но и после того как определился,
* если он не совпадает с текущим языком (а он задан, напомню, не пропущен)
* то идет чтение из базы данных записей транслита именно того языка,
* который был указан
* 
* иначе была, как раньше, фигня и несоответствие
* например, с русского на английский для русского языка шел транслит нормальный
* а та же транслитерация, но когда установлен английский, уже была другая
* 
* раньше транслит был прямой и обратный
* это, конечно, хорошо, в ряде случае, но вряд ли будет ситуация, когда нужно
* распарсить транслит обратно
* и даже в этом случае, ретранслит будет некорректный, некоторые буквы потеряются
* например: царь -> car -> цар или для другой раскладки царь -> tsar -> тсар
* или вот еще: лье - lye -> люе
* 
* отмена обратного транслита, кэширование и введение новых функций
* делает сам транслит легче и упрощает работу с языками, нет путаницы,
* например, для японского нужно думать о транслите с японского на другие языки
* и не надо думать о транслите с русского на японский
*/

class Translit extends Data {
	
	public $lang;
	
	public function __construct() {
	}
	
	public function init($set = null) {
		if (!$set) {
			$lang = Language::getInstance();
			$set = $lang -> get('lang');
			unset($lang);
		}
		$this -> lang = $set;
		unset($set);
	}
	
	public function read($from, $to) {
		
		$result = $this -> getData($from . '-' . $to);
		
		if (!$result) {
			
			$config = Config::getInstance();
			
			$cache = new Cache($config -> get('path:cache') . 'translit' . DS);
			$cache -> caching($config -> get('cache:translit'));
			$cache -> init($from . '-' . $to);
			
			$data = $cache -> read();
			
			if ($data) {
				
				$result = $data;
				$this -> addData($from, $result);
				
			} else {
				
				$db = Database::getInstance();
				$db -> collection('languages');
				$db -> driver -> filter -> addFilter('name', 'translit');
				$db -> driver -> filter -> addFilter('parents', $from);
				$db -> launch();
				
				$pre = $db -> data -> getFirstData();
				
				$db -> clear();
				
				foreach ($pre as $key => $item) {
					$result[$key] = $item[$to];
				}
				unset($key, $item);
				
				$this -> addData($from . '-' . $to, $result);
				
				unset($db, $pre);
				
			}
			
			$cache -> write( $result );
			
			unset($config, $cache, $data);
			
		}
		
		return $result;
		
	}
	
	public function launch($string, $to = null, $from = null) {
		
		if (!$string) {
			return null;
		}
		if (!$to) {
			$to = $this -> lang;
		}
		if (!$from) {
			$from = $this -> lang;
		}
		if ($to === $from) {
			return $string;
		}
		
		$result = $this -> read($from, $to);
		
		return Strings::replace(
			$string,
			Objects::keys($result),
			//Objects::values($result)
			$result
		);
		
	}
	
}

?>