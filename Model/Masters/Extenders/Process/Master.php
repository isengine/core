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

abstract class Master extends Data {
	
	// по $data для ссылок и тегов здесь общее правило такое:
	// пути и ключевые значения
	// затем классы
	// затем альты
	
	abstract public function launch($data);
	
}

?>