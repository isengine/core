<?php defined('isPROCESS') or die;

// создаем список папок для копирования

if (empty($nolocal)) {
	$folders[] = PATH_LOCAL;
}

// ассеты

$list = localList(PATH_ASSETS, ['return' => 'folders', 'skip' => NAME_TEMPLATES]);
foreach ($list as $item) {
	$folders[] = PATH_ASSETS . $item;
}
unset($item, $list);

// кастомные

$list = localList(PATH_CUSTOM, ['return' => 'folders', 'skip' => NAME_TEMPLATES]);
foreach ($list as $item) {
	$folders[] = PATH_CUSTOM . $item;
}
unset($item, $list);

// шаблоны

$list = localList(PATH_TEMPLATES, ['return' => 'folders', 'skip' => 'administrator:base:error:restore']);
foreach ($list as $item) {
	$folders[] = PATH_TEMPLATES . $item;
}
unset($item, $list);

// создаем список файлов из базы данных с исключениями

if (empty($simple)) {
	$folders[] = PATH_DATABASE;
} else {
	$skip = [
		'languages:en:common.ini', 
		'languages:ru:common.ini', 
		'languages:ru:dictionary.ini', 
		'languages:ru:morph.ini', 
		'functions.ini', 
		'langcodes.ini', 
		'processor.ini', 
		'rights.ini', 
		'users.ini', 
		'userstable.ini', 
		'functions', 
		'langcodes', 
		'processor', 
		'rights', 
		'users', 
		'userstable'
	];
	$list = localList(PATH_DATABASE, ['return' => 'files', 'subfolders' => true, 'fullpath' => true, 'skip' => $skip]);
	foreach ($list as $item) {
		$localdb[] = PATH_DATABASE . $item;
	}
	unset($item, $list);
}

?>