<?php defined('isENGINE') or die;

if (
	DEFAULT_MODE === 'develop' &&
	SECURE_BLOCKIP === 'developlist'
) {
	
	global $libraries;
	
	$icons = dbUse('icons', 'select', true);
	$path = PATH_LOCAL . $icons['settings']['path'] . DS;
	$original = $path . $icons['settings']['original'];
	$error = null;
	
	if (!extension_loaded('gd')) {
		$error[] = 'php extension \'GD\'';
	}
	if (!objectIs($libraries)) {
		$error[] = 'system libraries \'simpleimage\', \'php-ico\'';
	} elseif (!in('libraries', 'simpleimage:system')) {
		$error[] = 'system library \'simpleimage\'';
	} elseif (!in('libraries', 'php-ico:system')) {
		$error[] = 'system library \'php-ico\'';
	}
	if (empty($icons) || !file_exists($original)) {
		$error[] = 'needed icon settings';
	}
	if (!empty($error)) {
		logging('icons can not generated - ' . objectToString($error, ' and ') . ' is not set');
		return false;
	}
	
	if (!empty($icons['favicon'])) {
		
		$name = (empty($icons['favicon']['rootfolder']) ? $path : PATH_SITE) . (empty($icons['favicon']['name']) ? 'favicon' : $icons['favicon']['name']) . '.ico';
		
		if (!file_exists($name) || !empty($icons['settings']['update'])) {
			
			if (!objectIs($icons['favicon']['sizes'])) {
				$icons['favicon']['sizes'] = [[16, 16], [24, 24], [32, 32], [48, 48]];
			} else {
				foreach ($icons['favicon']['sizes'] as &$i) {
					$size = dataParse($i);
					if (empty($size[1])) {
						$size[1] = $size[0];
					}
					$i = [$size[0], $size[1]];
				}
			}
			
			$ico_lib = new PHP_ICO($original, $icons['favicon']['sizes']);
			$ico_lib->save_ico($name);
			
			unset($size, $i, $ico_lib);
			
		}
		
		unset($icons['favicon'], $name);
		
	}
	
	foreach ($icons as $key => $item) {
		
		if (
			empty($item['name']) ||
			!objectIs($item['sizes'])
		) {
			continue;
		}
		
		foreach ($item['sizes'] as $i) {
			
			$size = dataParse($i);
			
			if (empty($size[1])) {
				$size[1] = $size[0];
			}
			
			// 0 - width
			// 1 - height
			
			/*
			if ($key === 'msapplication') {
				if ($size[0] == '144') {
					$item['name'] = 'TileImage';
				} elseif ($size[0] !== $size[1]) {
					$item['name'] = 'wide' . $size[0] . 'x' . $size[1] . 'logo';
				} else {
					$item['name'] = 'square' . $size[0] . 'x' . $size[1] . 'logo';
				}
			} else {
				$item['name'] .= 'TileImage';
			}
			*/
			
			$name = $path . $item['name'] . '-' . $size[0] . 'x' . $size[1] . '.png';
			
			if (
				!file_exists($name) ||
				!empty($icons['settings']['update'])
			) {
				
				if ($key === 'msapplication') {
					$size[0] = ceil($size[0] * 1.8);
					$size[1] = ceil($size[1] * 1.8);
				}
				
				//echo '[' . $name . ' :: ' . print_r($size, true) . ']<br>';
				
				$image = new \claviska\SimpleImage();
				$image->fromFile(!empty($item['original']) ? $path . $item['original'] : $original);
				
				if (
					!empty($icons['settings']['resize']) &&
					$icons['settings']['resize'] === 'nocrop'
				) {
					if ($size[0] > $size[1]) {
						$image->resize(null, $size[1]);
					} else {
						$image->resize($size[0], null);
					}
					$image->bestFit($size[0], $size[1]);
				} elseif (
					!empty($icons['settings']['resize']) &&
					$icons['settings']['resize'] === 'crop'
				) {
					$image->thumbnail($size[0], $size[1]);
				} else {
					if ($size[0] > $size[1]) {
						$image->thumbnail($size[1], $size[1]);
					} else {
						$image->thumbnail($size[0], $size[0]);
					}
				}
				
				$image->toString('image/png');
				
				$newimage = new \claviska\SimpleImage();
				$newimage
					->fromNew($size[0], $size[1])
					->overlay($image)
					->toFile($name, 'image/png');
				
				unset($image, $newimage, $size, $name);
				
			}
			
		}
		
	}
	
	//echo '<br>';
	//print_r($icons);
	//echo '<hr>';
	
	unset($key, $item, $i, $icons);
	
}

?>