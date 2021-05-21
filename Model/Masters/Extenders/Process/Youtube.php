<?php

namespace is\Masters\Extenders\Process;

use is\Helpers\System;

class Youtube extends Master {
	
	public function launch($data) {
		
		$target = $data[1];
		
		if ($target === 'image') {
			$type = System::set($data[2]) ? $data[2] : 'maxresdefault';
			return 'https://img.youtube.com/vi/' . $data[0] . '/' . $type . '.jpg';
		} elseif ($target === 'link') {
			return 'https://www.youtube.com/watch?v=' . $data[0];
		} else {
			$width = System::set($data[1]) ? $data[1] : '100%';
			$height = System::set($data[2]) ? $data[2] : '100%';
			return '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $data[0] . '" title="YouTube Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		}
		
	}
	
}

?>