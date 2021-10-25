<?php

namespace is\Masters\Extenders\Tvars;

use is\Helpers\Strings;
use is\Helpers\Paths;
use is\Helpers\Local;

class Img extends Master {
	
	public function launch($data) {
		
		// с помощью srcset можно организовать правильный lazyload
		// для этого нужно установить js библиотеку
		// и указать изображению соответствующий класс
		
		// https://apoorv.pro/lozad.js/
		// <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>
		// lozad('.demilazyload').observe();
		// lozad( document.querySelector('img') ).observe();
		
		$result = Local::matchUrl($data[0], true);
		
		$url = $result ? $data[0] : $data[1];
		
		if (!$url) {
			return;
		}
		
		$srcset = $result ? $data[1] : null;
		if ($srcset) {
			$srcset = ' srcset="' . $srcset . '" data-srcset="' . $url . '"';
		}
		
		$class = $data[2] ? ' class="' . $data[2] . '"' : null;
		
		return '<img src="' . $url . '"' . $srcset . ' alt="' . $data[3] . '"' . $class . ' />';
		
	}
	
}

?>