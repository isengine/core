<?php defined('isENGINE') or die;

global $libraries;

if (
	!in('libraries', 'less') &&
	!in('libraries', 'less.php') &&
	!in('libraries', 'lessjs:system')
) {
	return;
}

// вводим доп.функцию для общей обработки файла less
if (DEFAULT_MODE === 'develop') {
	
	function func_less($name) {
		
		$path = PATH_ASSETS . 'less' . DS;
		$name = str_replace('/', DS, $name);
		
		if (
			!file_exists($path) ||
			!file_exists($path . $name . '.less')
		) {
			return false;
		}
		
		if (in('libraries', 'lessjs:system')) {
			
			return 'less';
			
		} elseif (in('libraries', 'less.php:wikimedia')) {
			
			if (!file_exists($path . $name . '.md5')) {
				$md5 = 0;
			} else {
				$md5 = file_get_contents($path . $name . '.md5');
			}
			
			if (
				md5_file($path . $name . '.less') !== $md5 &&
				filesize($path . $name . '.less') > 0
			) {
				
				$less = new Less_Parser(['compress' => true]);
				$less->parseFile($path . $name . '.less', URL_ASSETS . 'less/');
				$less = $less->getCss();
				file_put_contents($path . $name . '.css', $less);
				file_put_contents($path . $name . '.md5', md5_file($path . $name . '.less'));
				unset($less);
				
			}
			
			return 'compiled';
			
		} else {
			
			$less = new lessc;
			$less->checkedCompile($path . $name . '.less', $path . $name . '.css');
			unset($less);
			
			return 'compiled';
			
		}
		
		//echo '<link rel="stylesheet" type="text/css" href="' . URL_ASSETS . 'less/' . $name . '.css' . '" />';
		//unset($name, $path, $md5);
		
	}
}

global $loadingLog;
global $uri;

$prefix = in('options', 'updatelocal') && $template -> modified ? '?' . $template -> modified : '';
$print = null;

$print .= "\r\n\r\n" . '<!-- LESS -->' . "\r\n\r\n";

$link = [
	'css' => ['<link rel="stylesheet" rev="stylesheet" type="text/css" href="', '" />'],
	'less' => ['<link rel="stylesheet/less" type="text/css" href="', '" />'],
	'js' => ['<script type="text/javascript" src="', '"></script>']
];

$places = ['headopen'];

if (!empty($template -> device -> type)) {
	if ($template -> device -> type === 'desktop') {
		$places[] = 'desktop';
	} else {
		$places[] = 'mobile';
	}
}

if (!empty($template -> device -> os)) {
	$places[] = $template -> device -> os;
}

foreach ($places as $place) {
	
	if (objectIs($template -> settings -> assets[$place]['less'])) {
		
		foreach ($template -> settings -> assets[$place]['less'] as $item) {
			$result = null;
			if (DEFAULT_MODE === 'develop') {
				$result = func_less($item);
				if (file_exists(PATH_ASSETS . 'less' . DS . $item . '.less')) {
					$prefix = '?' . filemtime(PATH_ASSETS . 'less' . DS . $item . '.less');
				}
			}
			if ($result === 'less') {
				$print .= $link['less'][0] . URL_ASSETS . 'less/' . $item . '.less' . $prefix . $link['less'][1];
			} else {
				$print .= $link['css'][0] . URL_ASSETS . 'less/' . $item . '.css' . $prefix . $link['css'][1];
			}
		}
		
		unset($item, $result);
		
	}
	
}

$inner = thispage('is');

$result = null;

if (DEFAULT_MODE === 'develop') {
	$result = func_less('inner/' . $inner);
	if (file_exists(PATH_ASSETS . 'less' . DS . 'inner' . DS . $inner . '.less')) {
		$prefix = '?' . filemtime(PATH_ASSETS . 'less' . DS . 'inner' . DS . $inner . '.less');
	}
} elseif (in('libraries', 'lessjs:system')) {
	$result = 'less';
} else {
	$result = 'compiled';
}

if ($result === 'compiled' && file_exists(PATH_ASSETS . 'less' . DS . 'inner' . DS . $inner . '.css')) {
	$print .= $link['css'][0] . URL_ASSETS . 'less/inner/' . $inner . '.css' . $prefix . $link['css'][1];
} elseif ($result === 'less' && file_exists(PATH_ASSETS . 'less' . DS . 'inner' . DS . $inner . '.less')) {
	$print .= $link['less'][0] . URL_ASSETS . 'less/inner/' . $inner . '.less' . $prefix . $link['less'][1];
}

echo $print;

unset($link, $print, $prefix, $place, $places, $inner, $result);

if (in('libraries', 'lessjs:system')) {
	echo '<script>less = { env: \'development\'};</script>';
}

?>