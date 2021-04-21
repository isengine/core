<?php

namespace is\Model\Views\Process;

use is\Helpers\Strings;
use is\Model\Views\View;

class State extends Master {
	
	public function launch($data) {
		$view = View::getInstance();
		return $view -> get('state|' . Strings::join($data, ':'));
	}
	
}

?>