<?php

namespace is\Masters\Files\Script;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Helpers\Local;
use is\Parents\Data;
use is\Components\Router;
use is\Components\State;
use is\Components\Uri;
use is\Masters\View;
use is\Masters\Files\Master;

class Serviceworker extends Master {
	
	// https://habr.com/ru/post/358060/
	
	public function launch() {
		
		$view = View::getInstance();
		
		$webapp = $view -> get('state|settings:webapp');
		$icons = $view -> get('icon|data');
		
		$path = DI . (!empty($webapp['custompath']) ? str_replace(':', DS, $webapp['custompath']) . DS : null) . $webapp['serviceworker'];
		$sw = !empty($webapp['serviceworker']) && file_exists($path) ? Local::readFile($path) : null;
		
		//System::setHeaderCode(200);
		Sessions::setHeader(['Content-type' => 'application/javascript; charset=utf-8']);
		//header('Cache-Control: no-store, no-cache, must-revalidate');
		
		echo $sw;
		
		// read and echo content another file (as js) which set in template -> webapp
		// and print push-messages
		
	}
	
}
?>