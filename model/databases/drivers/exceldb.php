<?php

namespace is\Model\Databases\Drivers;

use is\Model\Databases\Driver;
use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use \SimpleXLSX;

class ExcelDB extends Driver {
	
	protected $path;
	
	public function connect() {
		
		$this -> path = preg_replace('/[\\/]+/ui', DS, DR . str_replace(':', DS, $this -> settings['name']) . DS);
		
	}
	
	public function close() {
		
	}
	
	public function launch() {
		
		/*
		protected $prepare;
		protected $settings;
		
		public $query; // тип запроса в базу данных - чтение, запись, добавление, удаление
		public $collection; // раздел базы данных
		
		public $id; // имя или имена записей в базе данных
		public $name; // имя или имена записей в базе данных
		public $type; // тип или типы записей в базе данных
		public $parents; // родитель или родители записей в базе данных
		public $owner; // владелец или владельцы записей в базе данных
		
		public $ctime; // дата и время (в формате unix) создания записи в базе данных
		public $mtime; // дата и время (в формате unix) последнего изменения записи в базе данных
		public $dtime; // дата и время (в формате unix) удаления записи в базе данных
		
		public $limit; // установить возвращаемое количество записей в базе данных
		*/
		
		if (!$this -> collection) {
			return;
		}
		
		if ($this -> query === 'read') {
			$this -> read();
		}
		
		// ЕЩЕ НУЖНО СДЕЛАТЬ ФИЛЬТРАЦИЮ И ОТБОР ПО УКАЗАННЫМ QUERY ДАННЫМ
		// ЕЩЕ НУЖНО createFileFromInfo
		// ДЛЯ ПОДГОТОВКИ ФАЙЛА К ЗАПИСИ
		
		//echo $name . '<br>';
		//echo print_r($query, 1) . '<br>';
		//echo '<pre>';
		//echo print_r($prepared, 1) . '<br>';
		//echo '</pre>';
		
	}
	
	public function hash() {
		$json = json_encode($this -> filter) . json_encode($this -> fields) . json_encode($this -> rights);
		$this -> hash = md5_file($this -> path . $this -> collection . '.xlsx') . '.' . md5($json) . '.' . Strings::len($json) . '.' . (int) $this -> settings['all'] . '.' . $this -> settings['limit'];
	}
	
	public function prepare() {
		
		$path = $this -> path . $this -> collection . '.xlsx';
		$stat = stat($path);
		$excel = SimpleXLSX::parse($path);
		
		//return [
		//	'parents' => Objects::convert(str_replace(DS, ':', Strings::unlast($item['path']))),
		//	'id' => $parse['id'],
		//	'name' => str_replace('--', '.', $parse['name']),
		//	'type' => Objects::convert(str_replace(['--', ' '], ['.', ':'], $parse['type'])),
		//	'owner' => Objects::convert(str_replace(['--', ' '], ['.', ':'], $parse['owner'])),
		//	'ctime' => $stat['ctime'],
		//	'mtime' => $stat['mtime'],
		//	'dtime' => $parse['dtime'],
		//];
		
		// Общие настройки
		
		$rowkeys = $this -> settings['rowkeys'] ? $this -> settings['rowkeys'] : 0;
		
		$rowskip = $this -> settings['rowskip'] ? (is_array($this -> settings['rowskip']) ? $this -> settings['rowskip'] : Objects::convert($this -> settings['rowskip'])) : [];
		
		$sheets = $this -> settings['sheets'] ? (is_array($this -> settings['sheets']) ? $this -> settings['sheets'] : Objects::convert($this -> settings['sheets'])) : Objects::keys($excel -> sheetNames());
		
		$keys = $excel -> rows()[$rowkeys];
		
		$count = 0;
		
		// Построчная обработка
		foreach ($sheets as $sheet) {
		foreach ($excel -> rows($sheet) as $index => $row) {
			
			if (
				$index === $rowkeys ||
				Match::equalIn($rowskip, $index)
			) {
				continue;
			}
			
			$entry = Objects::fill($keys, $row);
			//$entry = Objects::combine($row, $keys);
			
			// проверка по имени
			if (!$this -> verifyName($entry['name'])) {
				$entry = null;
			}
			
			if ($entry) {
				foreach ($entry as $k => $i) {
					/*
					// Это условие надо убрать, иначе будут биться любые строки
					// Нужно оставить разбор, как он был задан - через настройки контента
					// КСТАТИ, ЭТИ НАСТРОЙКИ ТАКЖЕ МОЖНО ВНЕСТИ В НАСТРОЙКИ ДРАЙВЕРА
					// И ТОГДА БУДЕТ ОЧЕНЬ КРУТО !!!
					// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
					// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
					// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
					// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
					// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
					// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
					if (
						Strings::match($i, ':') ||
						Strings::match($i, '|')
					) {
						$i = Parser::fromString($i);
					}
					*/
					if ($this -> settings['encoding']) {
						$i = mb_convert_encoding($i, 'UTF-8', $this -> settings['encoding']);
					}
					if (Strings::match($k, ':')) {
						// А вот это условие оставить - т.к. бьются только ключи и это правильно
						$levels = Parser::fromString($k);
						$entry = Objects::add($entry, Objects::level([], $levels, $i), true);
						unset($entry[$k], $levels);
					} elseif (Objects::match(['type', 'parents', 'owner'], $k)) {
						// Это условие тоже нужно оставить для базовых полей
						if (
							Strings::match($i, ':') ||
							Strings::match($i, '|')
						) {
							$entry[$k] = Parser::fromString($i);
						}
					}
				}
				unset($k, $i);
				
				// несколько обязательных полей
				if (!$entry['ctime']) {
					$entry['ctime'] = $stat['ctime'];
				}
				if (!$entry['mtime']) {
					$entry['mtime'] = $stat['mtime'];
				}
				
				// проверка по датам
				if (!$this -> verifyTime($entry)) {
					$entry = null;
				}
				
			}
			
			// контрольная проверка
			$count = $this -> verify($entry, $count);
			if (!System::set($count)) {
				break;
			}
			
		}
		unset($key, $item);
		}
		unset($sheet);
		
	}
	
}

?>