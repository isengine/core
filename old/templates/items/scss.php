<?php defined('isENGINE') or die;

global $libraries;
global $template;

if (!in('libraries', 'scss')) {
	return;
}

// в версиях PHP до 7, нельзя использовать 'use' внутри условий
use Leafo\ScssPhp\Compiler;

// вводим доп.функцию для общей обработки файла scss
if (DEFAULT_MODE === 'develop') {
	
	function func_scss($name) {
		
		$path = PATH_ASSETS . 'scss' . DS;
		$name = str_replace('/', DS, $name);
		
		if (
			!file_exists($path) ||
			!file_exists($path . $name . '.scss')
		) {
			return false;
		}
		
		if (!file_exists($path . $name . '.md5')) {
			$md5 = 0;
		} else {
			$md5 = file_get_contents($path . $name . '.md5');
		}
		
		if (
			md5_file($path . $name . '.scss') !== $md5 &&
			filesize($path . $name . '.scss') > 0
		) {
			
			$scss = new Compiler();
			$scss->setImportPaths($path);
			$scss->setFormatter('Leafo\ScssPhp\Formatter\Expanded');
			$scss = $scss->compile('@import "' . $name . '.scss";');
			file_put_contents($path . $name . '.css', $scss);
			file_put_contents($path . $name . '.md5', md5_file($path . $name . '.scss'));
			unset($scss);
			
		}
		
		//echo '<link rel="stylesheet" type="text/css" href="' . URL_ASSETS . 'scss/' . $name . '.css' . '" />';
		//unset($name, $path, $md5);
		
		return true;
		
	}
	
}

global $loadingLog;
global $uri;

$prefix = in('options', 'updatelocal') && $template -> modified ? '?' . $template -> modified : '';
$print = null;

$print .= "\r\n\r\n" . '<!-- SCSS -->' . "\r\n\r\n";

$link = [
	'css' => ['<link rel="stylesheet" rev="stylesheet" type="text/css" href="', '" />'],
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
	
	if (objectIs($template -> settings -> assets[$place]['scss'])) {
		
		foreach ($template -> settings -> assets[$place]['scss'] as $item) {
			if (DEFAULT_MODE === 'develop') {
				func_scss($item);
				if (file_exists(PATH_ASSETS . 'scss' . DS . $item . '.css')) {
					$prefix = '?' . filemtime(PATH_ASSETS . 'scss' . DS . $item . '.css');
				}
			}
			$print .= $link['css'][0] . URL_ASSETS . 'scss/' . $item . '.css' . $prefix . $link['css'][1];
		}
		
		unset($item, $result);
		
	}
	
}

$inner = thispage('is');

$result = null;

if (DEFAULT_MODE === 'develop') {
	$result = func_scss('inner/' . $inner);
	if (file_exists(PATH_ASSETS . 'scss' . DS . 'inner' . DS . $inner . '.css')) {
		$prefix = '?' . filemtime(PATH_ASSETS . 'scss' . DS . 'inner' . DS . $inner . '.css');
	}
} else {
	$result = true;
}

if ($result && file_exists(PATH_ASSETS . 'scss' . DS . 'inner' . DS . $inner . '.css')) {
	$print .= $link['css'][0] . URL_ASSETS . 'scss/inner/' . $inner . '.css' . $prefix . $link['css'][1];
}

echo $print;

unset($link, $print, $prefix, $place, $places, $inner, $result);

?>