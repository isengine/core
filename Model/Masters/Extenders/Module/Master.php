<?php

namespace is\Masters\Extenders\Module;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;

use is\Parents\Data;

abstract class Master extends Data {
	
	public $instance; // имя экземпляра модуля
	public $settings; // настройки
	public $path; // путь до папки модуля
	public $custom; // путь до кастомной папки модуля в app
	
	public function __construct(
		$instance,
		$settings,
		$path,
		$custom
	) {

		$this -> instance = $instance;
		$this -> settings = $settings;
		$this -> path = $path;
		$this -> custom = $custom;
		$this -> launch();
		
	}
	
	abstract public function launch();
	
}

?>