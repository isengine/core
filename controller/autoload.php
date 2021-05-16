<?php

// Рабочее пространство имен

namespace is;

// auto loading

$path = file_get_contents( DR . 'config' . DS . 'path.ini' );
$path = json_decode($path, true);

define('IS_API_PATH', $path['app'] ? DR . preg_replace('/[\:\/\\\\]+/ui', DS, $path['app'] . DS) : null);
define('IS_VENDOR_PATH', $path['vendors'] ? DR . preg_replace('/[\:\/\\\\]+/ui', DS, $path['vendors'] . DS) : null);

unset($path);

spl_autoload_register(function($class) {
	
	$array = explode('\\', $class);
	$is = reset($array) === 'is';
	
	if ($is) {
		
		array_shift($array);
		
		$folders = [
			realpath(__DIR__ . DS . DP . DP . 'framework') . DS . 'php' . DS,
			realpath(__DIR__ . DS . DP) . DS,
			IS_API_PATH
		];
		
		$file = mb_strtolower(implode(DS, $array)) . '.php';
		
		foreach ($folders as $item) {
			$path = $item . $file;
			if ($item && file_exists($path)) {
				require $path;
				break;
			}
		}
		unset(
			$item,
			$path,
			$file,
			$folders,
		);
		
	} elseif (IS_VENDOR_PATH) {
		
		$vendor = array_slice($array, 0, 2);
		$path = IS_VENDOR_PATH . mb_strtolower(implode(DS, $vendor)) . DS;
		
		if (file_exists($path . 'composer.json')) {
			
			$composer = file_get_contents($path . 'composer.json');
			$composer = json_decode($composer, true);
			$folders = $composer['autoload'];
			$v = implode('\\', $vendor) . '\\';
			
			$folder = $folders['psr-4'][$v];
			
			if ($folder) {
				
				$file[] = $path . preg_replace('/[\:\/\\\\]+/ui', DS, $folder . (array_slice($array, 2) ? implode(DS, array_slice($array, 2)) . '.php' : DS));
			}
			
			if ($folders['psr-0']) {
				foreach ($folder['psr-0'] as $key => $item) {
					$file[] = $path . preg_replace('/[\:\/\\\\]+/ui', DS, $item) . $key . DS;
				}
				unset($key, $item);
			}
			
			if ($folders['classmap']) {
				foreach ($folders['classmap'] as $item) {
					$file[] = $path . preg_replace('/[\:\/\\\\]+/ui', DS, $item);
				}
				unset($item);
			}
			
			unset(
				$folders,
				$folder,
				$composer,
				$v
			);
			
			foreach ($file as $item) {
				if (file_exists($item)) {
					if (is_dir($item)) {
						$files = scandir($item);
						foreach ($files as $i) {
							if (
								$i !== '.' &&
								$i !== '..' &&
								end(explode('.', $i)) === 'php'
							) {
								require $item . $i;
							}
						}
						unset($files, $i);
					} else {
						require $item;
					}
				}
			}
			unset($item, $file);
			
		}
		
		unset(
			$vendor,
			$path
		);
		
	}
	
	unset($is, $array);
	
});

// manual loading

$list = file_get_contents( DR . 'config' . DS . 'classes.ini' );
$list = json_decode($list, true);

if (!empty($list)) {
	foreach ($list as $item) {
		$item = IS_VENDOR_PATH . str_replace(['\\', '/', ':'], DS, $item);
		require $item;
	}
	unset($item);
}

unset($list);

?>