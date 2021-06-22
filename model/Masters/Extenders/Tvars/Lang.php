<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Strings;
use is\Components\Language;

class Lang extends Master {
	
	public function launch($data) {
		
		$lang = Language::getInstance();
		return $lang -> get( Strings::join($data, ':') );
		
	}
	
}

?>