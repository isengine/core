<?php

namespace is\Model\Views\Process;

use is\Helpers\Strings;
use is\Helpers\Paths;

class Img extends Master {
	
	public function launch($data) {
		
		// с помощью srcset можно организовать правильный lazyload
		// для этого нужно установить js библиотеку
		// и указать изображению соответствующий класс
		
		// https://apoorv.pro/lozad.js/
		// <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>
		// lozad('.demilazyload').observe();
		// lozad( document.querySelector('img') ).observe();
		
		$url = $data[0];
		if (Strings::find($url, '//') !== 0) {
			$url = Paths::prepareUrl($url);
		}
		
		$srcset = $data[1];
		if ($srcset) {
			$srcset = ' srcset="' . $srcset . '" data-srcset="' . $url . '"';
		}
		
		$class = $data[2] ? ' class="' . $data[2] . '"' : null;
		
		return '<img src="' . $url . '"' . $srcset . ' alt="' . $data[3] . '"' . $class . ' />';
		
	}
	
}

?>