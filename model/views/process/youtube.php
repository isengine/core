<?php

namespace is\Model\Views\Process;

class Youtube extends Master {
	
	public function launch($data) {
		
		return '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' . $data[0] . '" title="YouTube Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		
	}
	
}

?>