<?php defined('isPROCESS') or die;

// на данный момент этот процесс бекапит только файлы локальной базы данных
// на будущее, в частности для шаблонов, нужно делать так:
// читать настройки шаблона из базы данных и бэкапить оттуда все описатели библиотек в бд и локально ассеты

if (!(DEFAULT_MODE === 'develop' && SECURE_BLOCKIP === 'developlist')) {
	return false;
}

$nolocal = isset($process -> data['nolocal']) ? true : null;
$simple = isset($process -> data['simple']) ? true : null;
$templ = isset($process -> data['template']) ? (!empty($process -> data['templatename']) ? $process -> data['templatename'] : 'default') : null;
$nodef = !empty($templ) && $templ !== 'default' ? true : false;

$folders = [];
$files = [];
$localdb = [];

if (empty($templ)) {
	require_once 'default.php';
} else {
	require_once 'template.php';
}

// создаем архив

$zip = new ZipArchive();
$filename = PATH_SITE . $_SERVER['HTTP_HOST'] . date('.Y-m-d') . (empty($templ) ? (!empty($nolocal) ? '.nolocal' : null) : '.' . $templ) . '.backup.zip';

if (file_exists($filename)) {
	unlink($filename);
}

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
	exit("Can not open <$filename>\n");
}

// добавляем в архив список файлов из папок

//print_r($folders);

foreach ($folders as $item) {
	
	$i = localList($item, ['return' => 'files', 'subfolders' => true]);
	foreach ($i as $file) {
		
		$zip->addFile($item . $file, str_replace(PATH_SITE, '', $item . $file));
		//echo str_replace(PATH_SITE, '', $item . $file) . "\n";
		//echo $file . "\n";
	}
	
}

unset($item, $i, $file);

// добавляем в архив список файлов из папок

//print_r($files);

if (objectIs($files)) {
	foreach ($files as $file) {
		$zip->addFile($file, str_replace(PATH_SITE, '', $file));
	}
	unset($file);
}

// добавляем в архив список файлов из базы данных

//print_r($localdb);

if (!empty($localdb)) {
	
	foreach ($localdb as $item) {
		
		$zip->addFile($item, str_replace(PATH_SITE, '', $item . $file));
		//echo str_replace(PATH_SITE, '', $item . $file) . "\n";
		//echo $file . "\n";
		
	}
	
	unset($item);
	
}

// добавляем в архив список файлов из корневой папки

if (empty($templ)) {
	
	foreach (['configuration', 'developlist', 'blacklist', 'whitelist'] as $item) {
		$zip->addFile(PATH_SITE . $item . '.ini', $item . '.ini');
	}
	
	unset($item);
	
}

// закрываем архив

echo "numfiles: " . $zip->numFiles . "\n";
echo "status: " . $zip->status . "\n";
if (empty($templ)) {
	echo "local: " . (empty($nolocal) ? "include" : "not include") . "\n";
} else {
	echo "template: " . $templ . "\n";
}
$zip->close();

global $uri;
echo '<br>Backup process is complete! [<a href="' . $uri -> site . $uri -> previous . '">go back</a>]';

exit;

?>