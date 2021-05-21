<?php

namespace is\Masters\Extenders\Icon;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Parents\Data;
use is\Masters\Database;

class Icon extends Data {
	
	public function __construct() {
		
		// запуск родительского конструктора
		
		//parent::__construct();
		
		// инициализация настроек
		
		$db = Database::getInstance();
		
		$db -> collection('icon');
		$db -> driver -> filter -> addFilter('name', 'default');
		$db -> driver -> filter -> addFilter('type', 'settings');
		$db -> launch();
		
		$this -> setData( $db -> data -> getFirstData() );
		
		$db -> clear();
		
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