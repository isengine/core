<?php

namespace is\Model\Masters\Extenders\Process;

use is\Helpers\Strings;
use is\Model\Components\Datetime;

class Time extends Master {
	
	public function launch($data = null) {
		$time = Datetime::getInstance();
		return $time -> get( Strings::join($data, ':') );
	}
	
}

?>