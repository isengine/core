<?php

namespace is\Model\Views\Process;

use is\Helpers\Strings;
use is\Model\Components\Language;

class Lang extends Master {
	
	public function launch($data) {
		
		$lang = Language::getInstance();
		return $lang -> get( Strings::join($data, ':') );
		
	}
	
}

?>