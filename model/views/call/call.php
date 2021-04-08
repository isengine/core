<?php

namespace is\Model\Views\Call;

use is\Model\Parents\Data;
use is\Helpers\Local;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\System;
use is\Helpers\Prepare;
use is\Helpers\Match;
use is\Helpers\Paths;

class Call extends Data {
	
	// по $data для ссылок и тегов здесь общее правило такое:
	// пути и ключевые значения
	// затем классы
	// затем альты
	
	public function __construct() {
	}
	
	public function launch($string) {
		return Parser::textVariables($string, function($type, $data){
			$name = __NAMESPACE__ . '\\' . Prepare::upperFirst($type);
			$call = new $name;
			return $call -> launch($data);
		});
	}
	
}

?>