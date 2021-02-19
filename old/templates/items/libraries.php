<?php defined('isENGINE') or die;

global $loadingLog;
global $template;
global $libraries;
global $uri;

$place = empty($template -> place) ? 'headopen' : $template -> place;
$prefix = in('options', 'updatelocal') && $template -> modified ? '?' . $template -> modified : '';
$print = null;

$print .= "\r\n\r\n" . '<!-- LIBRARIES ' . ($place === 'headopen' ? '' : 'IN ' . strtoupper($place) . ' PLACE ') . '-->' . "\r\n\r\n";

$link = [
	'css' => ['<link rel="stylesheet" rev="stylesheet" type="text/css" href="', '" />'],
	'js' => ['<script ' . (in('options', 'oldbrowsers') ? 'type="text/javascript" ' : null) . 'src="', '"></script>']
];

$sorting = [
	'css' => null,
	'js' => null
];

foreach ($libraries as $item) {
	
	if (!set($item['data'])) {
		continue;
	}
	
	$cdn = !empty($item['variant']) && strpos($item['variant'], 'cdn') !== false ? true : false;
	
	foreach ($item['data'] as $k => $i) {
		if (objectIs($i) && !empty($link[$k])) {
			
			if (
				$k === 'js' && (
					!empty($item['place']) && $item['place'] !== $place ||
					empty($item['place']) && $place !== 'headopen'
				) ||
				$k !== 'js' && $place !== 'headopen'
			) {
				continue;
			}
			
			foreach ($i as $str) {
				
				$s = substr($str, 0, strrpos($str, '.' . $k)) . '.min.' . $k;
				if (!$cdn && file_exists(PATH_LIBRARIES . datapath($s))) {
					$str = $s;
				}
				$s = $link[$k][0] . (!$cdn ? URL_LIBRARIES : null) . $str . (!$cdn ? $prefix : null) . $link[$k][1];
				
				if (in('options', 'sortinghead')) {
					$sorting[$k] .= $s;
				} else {
					$print .= $s;
				}
				
				unset($s);
				
			}
		}
	}
	
	if (isset($loadingLog)) { $loadingLog .= $item['name'] . ' library' . (!$cdn ? ' from cdn' : '') . ' is loading in' . $place . ' place\n'; }
	
	unset($k, $i, $str);
	
}

if (in('options', 'sortinghead')) {
	foreach ($sorting as $item) {
		$print .= $item;
	}
}

unset($link, $sorting, $item);

if (isset($loadingLog)) { $loadingLog .= '\n'; }

echo $print;
unset($print, $prefix);

?>