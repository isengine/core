<?php defined('isENGINE') or die;

global $loadingLog;
global $uri;

$print = "\r\n\r\n" . '<!-- STYLES -->' . "\r\n\r\n";

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
	if (objectIs($template -> settings -> assets[$place]['css'])) {
		foreach ($template -> settings -> assets[$place]['css'] as $item) {
			
			$prefix = localLink(
				'css/' . $item . '.css',
				'assets',
				in('options', 'updatelocal') && $template -> modified ? $template -> modified : true,
				'min'
			);
			
			if (!empty($prefix)) {
				$print .= $link['css'][0] . $prefix . $link['css'][1];
			}
			
			unset($prefix);
			
		}
		unset($item);
	}
}

$inner = thispage('is');

$prefix = localLink(
	'css/' . 'inner' . '/' . $inner . '.css',
	'assets',
	in('options', 'updatelocal') && $template -> modified ? $template -> modified : true,
	'min'
);

if (!empty($prefix)) {
	$print .= $link['css'][0] . $prefix . $link['css'][1];
}

echo $print;

unset($link, $print, $prefix, $place, $places, $inner);

//print_r($template -> settings -> assets);

?>