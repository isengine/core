<?php defined('isPROCESS') or die;

// создаем список папок для копирования

if (file_exists(PATH_LOCAL . DS . $templ)) {
	$folders[] = PATH_LOCAL . DS . $templ;
}

// ассеты

$list = localList(PATH_ASSETS, ['return' => 'folders', 'skip' => 'content:core:modules:' . NAME_TEMPLATES]);
foreach ($list as $item) {
	
	$itemlist = localList(PATH_ASSETS . $item, ['return' => 'folders']);
	foreach ($itemlist as $subitem) {
		if (strpos($subitem, $templ) === 0) {
			$folders[] = PATH_ASSETS . $item . $subitem;
		}
	}
	
}
unset($item, $list, $subitem, $itemlist);

// кастомные

$list = localList(PATH_CUSTOM, ['return' => 'folders', 'skip' => 'content:core:modules:' . NAME_TEMPLATES]);
foreach ($list as $item) {
	
	$itemlist = localList(PATH_CUSTOM . $item, ['return' => 'folders']);
	foreach ($itemlist as $subitem) {
		if (strpos($subitem, $templ) === 0) {
			$folders[] = PATH_CUSTOM . $item . $subitem;
		}
	}
	
}
unset($item, $list, $subitem, $itemlist);

// модули

$list = localList(PATH_CUSTOM . 'modules' . DS, ['return' => 'files', 'subfolders' => true, 'mask' => $templ . '.php']);
foreach ($list as $item) {
	$files[] = PATH_CUSTOM . 'modules' . DS . $item . $subitem;
}
unset($item, $list, $subitem, $itemlist);

// шаблоны

$folders[] = PATH_TEMPLATES . $templ . DS;

// создаем список файлов из базы данных с исключениями

$list = localList(PATH_DATABASE . 'languages' . DS, ['return' => 'files', 'subfolders' => true, 'mask' => $templ . '.ini']);
foreach ($list as $item) {
	//if (strpos($item, '.' . $templ . '.ini') !== false) {
		$localdb[] = PATH_DATABASE . 'languages' . DS . $item;
	//}
}
$list = localList(PATH_DATABASE . 'modules' . DS, ['return' => 'files', 'subfolders' => true, 'mask' => $templ . '.ini']);
foreach ($list as $item) {
	//if (strpos($item, '.' . $templ . '.ini') !== false) {
		$localdb[] = PATH_DATABASE . 'languages' . DS . $item;
	//}
}
unset($item, $list);

?>