<?php

namespace is\Masters\Extenders\Process;

use is\Helpers\Strings;
use is\Masters\View;

class State extends Master {
	
	public function launch($data) {
		$view = View::getInstance();
		return $view -> get('state|' . Strings::join($data, ':'));
	}
	
}

?>