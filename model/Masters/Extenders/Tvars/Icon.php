<?php

namespace is\Masters\Extenders\Tvars;

class Icon extends Master {
	
	public function launch($data) {
		
		return '<i class="' . $data[0] . '" aria-hidden="true"></i>';
		
	}
	
}

?>