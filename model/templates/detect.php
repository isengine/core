<?php

namespace is\Model\Templates;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

class Detect {
	
	public $type;
	public $os;
	public $screen;
	
	public function init() {
		
		$mobiledetect = new \Detection\MobileDetect;
		
		$this -> type = $mobiledetect->isMobile() ? ($mobiledetect->isTablet() ? 'tablet' : 'mobile') : 'desktop';
		
		if ( $mobiledetect->isWindowsPhoneOS() ) {
			$this -> os = 'windowsphone';
		} elseif ( $mobiledetect->isiOS() ) {
			$this -> os = 'ios';
		} elseif ( $mobiledetect->isAndroidOS() ) {
			$this -> os = 'android';
		}
		
		unset($mobiledetect);
		
	}
	
	public function match($name = null, $compare = null) {
		
		if (!$name) {
			return $this;
		}
		
		$result = $this -> $name;
		
		return $compare ? $result === $compare : $result;
		
	}
	
}

?>