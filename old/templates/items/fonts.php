<?php defined('isENGINE') or die;

global $loadingLog;
global $uri;

//<link href="https://fonts.googleapis.com/css?family=Cuprum|Playfair+Display:400,700&display=swap&subset=cyrillic,cyrillic-ext" rel="stylesheet">

$print = null;

$print .= "\r\n\r\n" . '<!-- FONTS -->' . "\r\n\r\n";

$link = [
	'fonts' => ['<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=', '" />', '&display=swap', '&subset='],
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
	
	if (objectIs($template -> settings -> assets[$place]['fonts']['list'])) {
		
		$print .= $link['fonts'][0];
		
		foreach ($template -> settings -> assets[$place]['fonts']['list'] as $key => $item) {
			$print .= htmlentities(($key > 0 ? '|' : null) . $item[0] . (!empty($item[1]) ? ':' . $item[1] : null));
		}
		unset($item);
		
		$print .= $link['fonts'][2];
		
		if (objectIs($template -> settings -> assets[$place]['fonts']['langs'])) {
			$print .= $link['fonts'][3] . objectToString($template -> settings -> assets[$place]['fonts']['langs'], ',');
		}
		
		$print .= $link['fonts'][1];
		
	}
	
}

echo $print;

unset($link, $print, $place, $places);

//print_r($template -> settings -> assets);

?>