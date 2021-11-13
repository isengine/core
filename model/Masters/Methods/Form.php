<?php

namespace is\Masters\Methods;

use is\Helpers\Local;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\System;
use is\Helpers\Prepare;
use is\Helpers\Match;

use is\Components\Config;
use is\Components\State;

use is\Masters\Module;

class Form extends Master {
	
	public $settings;
	public $errors;
	
	public function settings($field = 'instance') {
		
		$instance = Parser::fromString($this -> getData($field));
		
		if (
			!$instance ||
			!System::typeIterable($instance) ||
			Objects::len($instance) < 3
		) {
			return;
		}
		
		$config = Config::getInstance();
		$state = State::getInstance();
		$module = Module::getInstance();
		
		$module -> init(
			$config -> get('path:vendors'),
			$config -> get('path:app') . 'Masters' . DS . 'Modules' . DS,
			//$config -> get('path:cache') . 'modules' . DS,
			//$config -> get('cache:modules')
			null,
			null
		);
		
		$module -> launch($instance[1] . ':' . $instance[0], $instance[2] . ':' . 'settings');
		unset ($module);
		
		$sets = Parser::fromJson($state -> get('form'));
		
		if ($sets && is_array($sets)) {
			$this -> settings = $sets;
		}
		
	}
	
	public function fields() {
		Objects::each($this -> settings, function($item, $key){
			$name = $key;
			$options = &$item['options'];
			$value = $this -> getData($name);
			$change = null;
			
			if (System::typeIterable($item['data'])) {
				$change = true;
				$value = $item['data'][$value];
			}
			
			if ($value && !$change && $options['filter']) {
				$filter = Strings::split($filter, ':');
				foreach ($filter as $item) {
					if (Strings::match($value, $item)) {
						$change = true;
						$value = Strings::replace($value, $item, null);
						$this -> error($name, 'filter');
					}
				}
				unset($item);
			}
			
			if ($value && $options['prepare']) {
				$change = true;
				$value = $this -> prepareFields($value, $options['prepare']);
			}
			
			if ($value && $options['validate']) {
				$verify = $this -> prepareFields($value, $options['validate']);
				if ($value !== $verify) {
					$change = true;
					$value = $verify;
					$this -> error($name, 'validate');
				}
				unset($verify);
			}
			
			if ($value && $item['min'] && $value < $item['min']) {
				$change = true;
				$value = $item['min'];
				$this -> error($name, 'min');
			}
			
			if ($value && $item['max'] && $value > $item['max']) {
				$change = true;
				$value = $item['max'];
				$this -> error($name, 'max');
			}
			
			if ($value && $item['minlength'] && Strings::len($value) < $item['minlength']) {
				$this -> error($name, 'minlength');
			}
			
			if ($value && $item['maxlength'] && Strings::len($value) > $item['maxlength']) {
				$change = true;
				$value = Strings::get($value, 0, $item['maxlength']);
				$this -> error($name, 'maxlength');
			}
			
			if (!System::set($value) && $item['required']) {
				$this -> error($name, 'required');
			}
			
			if ($change) {
				$this -> setData($name, $value);
			}
			
			return $item;
		});
	}
	
	public function prepareFields($data, $prepare) {
		$prepare = Objects::convert($prepare);
		foreach ($prepare as $item) {
			$data = Prepare::$item($data);
		}
		unset($item);
		return $data;
	}
	
	public function antispam($field = 'antispam') {
		
		// определение спам-ботов по заполнению скрытого поля,
		// которое должно присутствовать в форме,
		// но должно оставаться пустым
		
		// возвращает результат проверки
		// а что делать с этим результатом - ваша реализация
		
		return !System::exists($this -> getData($field)) || System::set($this -> getData($field));
		
	}
	
	public function error($field, $message = null) {
		
		// запись ошибки с указанием имени поля в качестве ключа
		// если описании пустое, то в значение тоже будет записано имя поля
		
		$this -> errors[$filed][] = $message ? $message : $field;
		
	}
	
}

?>