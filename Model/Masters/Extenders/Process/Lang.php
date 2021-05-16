<?php

namespace is\Model\Masters\Extenders\Process;

use is\Helpers\Strings;
use is\Model\Components\Language;

class Lang extends Master {
	
	public function launch($data) {
		
		$lang = Language::getInstance();
		return $lang -> get( Strings::join($data, ':') );
		
	}
	
}

?>