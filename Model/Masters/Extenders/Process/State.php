<?php

namespace is\Model\Masters\Extenders\Process;

use is\Helpers\Strings;
use is\Model\Masters\View;

class State extends Master {
	
	public function launch($data) {
		$view = View::getInstance();
		return $view -> get('state|' . Strings::join($data, ':'));
	}
	
}

?>