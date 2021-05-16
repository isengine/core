<?php

namespace is\Model\Masters\Extenders\Process;

use is\Helpers\Prepare;
use is\Model\Components\Language;

class Phone extends Master {
	
	public function launch($data) {
		
		$url = $data[0];
		$class = $data[1] ? ' class="' . $data[1] . '"' : null;
		
		if (!$data[2]) {
			$data[2] = $url;
		}
		
		$lang = Language::getInstance();
		$url = Prepare::phone($url, $lang -> get('lang'));
		
		return '<a href="tel:' . $url . '" alt="' . $data[2] . '"' . $class . '>' . $data[2] . '</a>';
		
	}
	
}

?>