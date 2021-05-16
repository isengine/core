<?php

namespace is\Model\Masters\Extenders\Process;

use is\Helpers\Strings;

class Url extends Master {
	
	public function launch($data) {
		
		$url = $data[0];
		$absolute = Strings::find($url, '//') === 0 ? ' target="_blank"' : null;
		$class = $data[1] ? ' class="' . $data[1] . '"' : null;
		
		return '<a href="' . $url . '" alt="' . $data[2] . '"' . $class . $absolute . '>' . $data[2] . '</a>';
		
	}
	
}

?>