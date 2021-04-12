<?php

namespace is\Model\Views\Process;

class Mail extends Master {
	
	public function launch($data) {
		
		$url = $data[0];
		$class = $data[1] ? ' class="' . $data[1] . '"' : null;
		
		if (!$data[2]) {
			$data[2] = $url;
		}
		
		$subject = $data[3] ? '?subject=' . $data[3] : null;
		
		return '<a href="mailto:' . $url . $subject . '" alt="' . $data[2] . '"' . $class . '>' . $data[2] . '</a>';
		
	}
	
}

?>