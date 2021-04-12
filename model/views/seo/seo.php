<?php

namespace is\Model\Views\Seo;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Model\Parents\Data;
use is\Model\Databases\Database;
use is\Model\Components\Router;

use is\Model\Views\Process\Process;

class Seo extends Process {
	
	public function __construct() {
		
		// запуск родительского конструктора
		
		parent::__construct();
		
		// инициализация настроек
		
		$router = Router::getInstance();
		
		$db = Database::getInstance();
		
		$db -> collection('seo');
		$db -> driver -> filter -> addFilter('name', 'default');
		$db -> driver -> filter -> addFilter('type', 'settings');
		$db -> launch();
		
		$this -> setData( $db -> data -> getFirstData() );
		
		$db -> clear();
		
		$page = System::typeClass($router -> current, 'entry') ? $router -> current -> getEntryData('name') : 'index';
		
		if ($page) {
			$db -> driver -> filter -> addFilter('name', '+' . $page);
			$db -> launch();
			
			$this -> mergeData( $db -> data -> getFirstData() );
			
			$db -> clear();
		}
		
		$this -> launchByData();
		
	}
	
	public function title() {
		$this -> addData(
			'fulltitle',
			$this -> getData('pre') . $this -> getData('title') . $this -> getData('post')
		);
	}
	
	public function keys() {
		
		$keys = $this -> getData('keywords');
		
		if (!System::typeIterable($keys)) {
			$keys = Strings::split($keys, ',');
		}
		
		if (!System::typeIterable($keys)) {
			$data = Strings::split($this -> getData('description'));
			if (System::typeIterable($data)) {
				$c = 0;
				$limit = 200;
				foreach ($data as $item) {
					$item = Prepare::clear($item);
					$item = Prepare::words($item);
					$len = Strings::len($item);
					if ($len > 4) {
						$keys[] = $item;
					}
					$c += $len;
					if ($c >= $limit) {
						break;
					}
				}
				unset($item);
			}
		}
		
		$this -> addData('tags', $keys);
		$this -> addData('keywords', Strings::join($keys, ', '));
		
	}
	
}

?>