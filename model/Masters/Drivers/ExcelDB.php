<?php

namespace is\Masters\Drivers;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use \SimpleXLSX;

class ExcelDB extends Master {
	
	protected $path;
	protected $parent;
	
	public function connect() {
		
		$this -> path = preg_replace('/[\\/]+/ui', DS, DR . str_replace(':', DS, $this -> settings['name']) . DS);
		
	}
	
	public function close() {
		
	}
	
	public function parents($parent) {
		if (!System::set($parent) && !System::typeIterable($parent)) {
			return;
		}
		$this -> parent = System::typeIterable($parent) ? $parent : Strings::split($parent, ':');
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
		$path = $this -> path . $this -> collection . ($this -> parent ? DS . Strings::join($this -> parent, DS) : null) . '.xlsx';
		$this -> hash = (Local::matchFile($path) ? md5_file($path) : null) . '.' . md5($json) . '.' . Strings::len($json) . '.' . (int) $this -> settings['all'] . '.' . $this -> settings['limit'];
	}
	
	public function prepare() {
		
		$path = $this -> path . $this -> collection . ($this -> parent ? DS . Strings::join($this -> parent, DS) : null) . '.xlsx';
		
		if (!Local::matchFile($path)) {
			return;
		}
		
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
		
		// rowkeys - номер строки, откуда берутся ключи или массив с ключами
		// rowskip - номера строк (в виде текста или массива) которые нужно пропустить (обычно rowkeys тоже должен сюда входить)
		// colskip - номера колонок (в виде текста или массива) которые нужно пропустить
		// sheets - номера листов (в виде текста или массива) которые нужно обработать
		// fill - номер колонки, который будет браться для заполнения (см. метод 'cols' в мастере)
		
		// например:
		// "rowkeys" : "6",
		// "rowkeys" : ["data:title", "data:price", "data:units"],
		// "rowskip" : "0:1:2:3:4:5:6:7:8:9:10",
		// "colskip" : "0:2",
		// "fill" : 1,
		
		$rowkeys = $this -> settings['rowkeys'] ? $this -> settings['rowkeys'] : 0;
		
		$rowskip = $this -> settings['rowskip'] ? (is_array($this -> settings['rowskip']) ? $this -> settings['rowskip'] : Objects::convert($this -> settings['rowskip'])) : [];
		
		$colskip = $this -> settings['colskip'] ? (is_array($this -> settings['colskip']) ? $this -> settings['colskip'] : Objects::convert($this -> settings['colskip'])) : [];
		$colskip_true = System::set($colskip);
		
		$keys = System::typeOf($rowkeys, 'iterable') ? $rowkeys : $excel -> rows()[$rowkeys];
		
		$sheets = System::set($this -> settings['sheets']) ? (is_array($this -> settings['sheets']) ? $this -> settings['sheets'] : Objects::convert($this -> settings['sheets'])) : Objects::keys($excel -> sheetNames());
		
		System::debug($sheets, '!q');
		
		$fill = System::typeOf($this -> settings['fill'], 'scalar') ? $this -> settings['fill'] : null;
		$fill_val = null;
		
		$count = 0;
		
		// Построчная обработка
		foreach ($sheets as $sheet) {
		foreach ($excel -> rows($sheet) as $index => $row) {
			
			//System::debug($row, '!q');
			
			// пропускаем номера строк, которые были заданы в настройках
			if (
				$index === $rowkeys ||
				Match::equalIn($rowskip, $index)
			) {
				continue;
			}
			
			// заполняем fill
			// это специальная переменная для автоподстановки значений
			// например, заголовок групп
			// если в настройках задан fill, то проверяем значение колонок
			// все должны быть пустыми, кроме одного, заданного ключом fill
			// если это так, то значение записываем в переменную fill_val,
			// которое потом можем подставлять при создании колонок и обработке текущих
			// после этого пропускаем эту строку
			$fill_row = $row;
			Objects::clear($fill_row);
			if (
				Objects::len($fill_row) === 1 &&
				Objects::first($fill_row, 'key') === $fill
			) {
				$fill_val = Objects::first($fill_row, 'value');
				continue;
			}
			unset($fill_row);
			
			// пропускаем номера колонок, которые были заданы в настройках
			if ($colskip_true) {
				$row = Objects::removeByIndex($row, $colskip);
			}
			
			$entry = Objects::join($keys, $row);
			//$entry = Objects::combine($row, $keys);
			
			// создание новых колонок и обработка текущих
			$this -> cols($entry, $fill_val);
			
			// проверка по имени
			if (!$this -> verifyName($entry['name'])) {
				$entry = null;
			}
			
			if ($entry) {
				foreach ($entry as $k => $i) {
					
					// Это условие должно оставаться выключенным, иначе будут биться любые строки
					// Если нужно преобразование, см. ниже
					// if (
					//   Strings::match($i, ':') ||
					//   Strings::match($i, '|')
					// ) {
					//   $i = Parser::fromString($i);
					// }
					
					// Мы сделали разбор строк и колонок, как и хотели, в мастере драйвера
					// Теперь через группу настроек 'cols' можно задавать новые колонки, обработку,
					// задавать значение по-умолчанию и преобразовывать в массив или в строку
					// Теперь не нужно задавать это в настройках контента - так намного мощнее
					
					if ($this -> settings['encoding']) {
						$i = mb_convert_encoding($i, 'UTF-8', $this -> settings['encoding']);
					}
					if (Strings::match($k, ':')) {
						// А вот это условие оставить - т.к. бьются только ключи и это правильно
						$levels = Parser::fromString($k);
						$entry = Objects::add($entry, Objects::inject([], $levels, $i), true);
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
				if (!$entry['parents']) {
					$entry['parents'] = $this -> parent;
				}
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