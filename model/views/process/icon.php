<?php

namespace is\Model\Views\Process;

class Icon extends Master {
	
	public function launch($data) {
		
		return '<i class="' . $data[0] . '" aria-hidden="true"></i>';
		
	}
	
}

?>