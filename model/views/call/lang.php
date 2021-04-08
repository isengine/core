<?php

namespace is\Model\Views\Call;

use is\Helpers\Strings;
use is\Model\Components\Language;

class Lang extends Master {
	
	public function launch($data) {
		
		$lang = Language::getInstance();
		return $lang -> get( Strings::join($data, ':') );
		
	}
	
}

?>