<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers;

class Prepare extends Master {
	
	public function launch($data) {
		
		$name = $data[0];
		return Helpers\Prepare::$name($data[1]);
		
	}
	
}

?>