<?php

namespace is\Masters\Extenders\Map;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Parents\Data;
use is\Masters\Database;
use is\Components\Router;
use is\Components\Cache;
use is\Components\Config;

class Map extends Data {
	
	public $cache;
	
	public function __construct() {
		
		// инициализация настроек
		
		$config = Config::getInstance();
		$path = $config -> get('path:cache') . 'map' . DS;
		
		$cache = new Cache($path);
		
		$db = Database::getInstance();
		$db -> collection('content');
		
		$db -> launch();
		$this -> setData( $db -> data -> getMap() );
		$db -> clear();
		
		$router = Router::getInstance();
		
		//System::debug($router -> structure);
		

		//name
		//path
		//title
		
		
	}
	
}

?>