<?php defined('isENGINE') or die;

global $content;
global $seo;
global $template;
global $lang;

$seo = [
	'title' => null,
	'h1' => null,
	'description' => null,
	'keywords' => null,
	'tags' => [],
	'copyright' => null,
	'author' => null,
	'created' => null,
	'modified' => null,
	'image' => '',
	'langs' => [],
	'content' => null,
	'options' => null,
	'filter' => null
];

$default = dbUse('seo:default' . set($template -> page['name'], ':' . $template -> page['name']), 'select', true);

if (!empty($default['default'])) {
	$seo = array_merge($seo, $default['default']);
}
if (!empty($default[$template -> page['name']])) {
	$seo = array_merge($seo, $default[$template -> page['name']]);
}

unset($default);

//if (!empty($seo['keywords']) && !is_array($seo['keywords'])) {
//	$seo['keywords'] = datasplit($seo['keywords']);
//	$seo['keywords'] = array_unique($seo['keywords']);
//}

if (objectIs($seo['keywords'])) {
	$seo['keywords'] = array_unique($seo['keywords']);
	$seo['keywords'] = objectToString($seo['keywords'], ', ');
}

if (
	empty($seo['langs']) &&
	objectIs($lang -> settings)
) {
	foreach ($lang -> settings as $key => $item) {
		if ($key !== $lang -> lang) {
			$seo['langs'][$key] = $item['code'];
		}
	}
}
if (objectIs($seo['langs'])) {
	if (!empty($lang) && !empty($lang -> lang)) {
		unset($seo['langs'][$lang -> lang]);
	}
	if (defined('DEFAULT_LANG') && DEFAULT_LANG !== true) {
		unset($seo['langs'][DEFAULT_LANG]);
	}
	
}
unset($item);

$seo = (object) $seo;

// autoseo для image

if (!empty($seo -> image) && mb_strpos($seo -> image, '}') !== false) {
	
	$seo -> image = preg_replace_callback('/{([^\{\}]+)?}/ui', function($i) {
		
		global $uri;
		$i = dataParse($i[1]);
		$ti = objectIs($i) ? array_shift($i) : $i;
		$ti = $uri -> site . mb_substr(constant('URL_' . strtoupper($ti)), 1) . objectToString($i, '/');
		return $ti;
		
	}, $seo -> image);
	
	unset($i);
	
}

// autoseo для keywords

if (empty($seo -> keywords) && objectIs($seo -> tags)) {
	$seo -> keywords = objectToString($seo -> tags, ', ');
}

// autoseo для контента

if (objectGet('content', 'name') && objectIs($seo -> content)) {
	
	$seo -> title = null;
	$seo -> description = null;
	$seo -> keywords = null;
	$seo -> tags = [];
	$seo -> created = null;
	$seo -> modified = null;
	$seo -> author = null;
	$seo -> image = null;
	
	$first = objectGet('content', 'first');
	
	$seo -> title = $first['data'][$seo -> content['title']];
	
	//$description = trim(mb_substr($first['data'][$seo -> content['description']], 0, 150));
	$description = $first['data'][$seo -> content['description']];
	if (objectIs($description)) { $description = objectToString($description, ', '); }
	$description = trim(mb_substr($description, 0, 150));
	$description = mb_substr($description, 0, mb_strrpos($description, ' '));
	$description = clear($description, 'notags');
	
	$i = 10;
	while (preg_match('/[^\w]/u', mb_substr($description, -1)) && $i > 0) {
		$description = mb_substr($description, 0, -1);
		$i--;
	}
	
	$seo -> description = $description . '&#8230;';
	
	$keywords = mb_strtolower($seo -> title . ' ' . $description);
	$keywords = preg_replace('/(\&\w+\;)|(\%\w+)|(\d+)/u', '', $keywords);
	$keywords = datasplit($keywords, '^\w');
	$keywords = array_unique($keywords);
	
	$i = 0;
	foreach ($keywords as $item) {
		if (mb_strlen($item) > 4) {
			$seo -> tags[] = mb_strtolower($item);
			$i++;
		}
		if ($i > 10) {
			break;
		}
	}
	$seo -> keywords = objectToString($seo -> tags, ', ');
	unset($i, $item, $keywords, $description);
	
	$seo -> created = [
		'w3cdtf' => date(DATE_W3C, $first['ctime']),
		'full' => gmdate('D, d M Y H:i:s \G\M\T', $first['ctime']),
		'short' => date('Y-m-d', $first['ctime']),
	];
	$seo -> modified = [
		'w3cdtf' => date(DATE_W3C, $first['mtime']),
		'full' => gmdate('D, d M Y H:i:s \G\M\T', $first['mtime']),
		'short' => date('Y-m-d', $first['mtime']),
	];
	
	$seo -> author = !empty($seo -> content['author']) && !empty($first['data'][$seo -> content['author']]) ? $first['data'][$seo -> content['author']] : null;
	$seo -> image = !empty($seo -> content['image']) && !empty($first['data'][$seo -> content['image']]) ? $first['data'][$seo -> content['image']] : null;
	
	unset($first);
	
}

// autoseo для остальных случаев
// заполняем пустые значения значениями из языкового массива

if (set($lang -> data) && objectIs($seo -> lang)) {
	
	foreach ($seo -> lang as $key => $item) {
		
		//echo $key . ':' . print_r($seo -> $key, 1) . '<br>';
		
		if (empty($seo -> $key)) {
			
			$target = preg_replace_callback('/{([^\{\}]+)?}/ui', function($i) {
				
				global $lang;
				
				$i = dataParse($i[1], 'line');
				$ti = objectIs($i) ? array_shift($i) : $i;
				
				if ($ti === 'page') {
					$ti = thispage('is');
					$t = lang('menu:' . $ti);
					return !empty($t) ? $t : $ti;
				}
				
				$t = $lang -> data[$ti];
				
				if (!empty($t)) {
					foreach ($i as $ii) {
						if (is_array($t)) {
							$t = $t[$ii];
						} else {
							break;
						}
					}
				}
				
				return $t;
				
			}, $item);
			
			$seo -> $key = clear($target, 'tospaces');
			unset($i, $target);
			
			//echo '[' . $item . ']<br>';
		}
		
	}
	
	unset($key, $item);
}

// autoseo для title
// заполняем пустое значение заголовка значением из языкового массива меню

if (empty($seo -> title) && !empty($lang -> data['menu'])) {
	$seo -> title = $lang -> data['menu'][$template -> page['name']];
}

// autoseo для h1
// заполняем пустое значение значением из заголовка или описания
if ($seo -> h1 === true && !empty($seo -> title)) {
	$seo -> h1 = $seo -> title;
}

// autoseo для дат
// заполняем пустое значение датами из шаблона

if (empty($seo -> created)) {
	$seo -> created = [
		'w3cdtf' => date(DATE_W3C, $template -> created),
		'full' => gmdate('D, d M Y H:i:s \G\M\T', $template -> created),
		'short' => date('Y-m-d', $template -> created),
	];
}

if (empty($seo -> modified)) {
	$seo -> modified = [
		'w3cdtf' => date(DATE_W3C, $template -> modified),
		'full' => gmdate('D, d M Y H:i:s \G\M\T', $template -> modified),
		'short' => date('Y-m-d', $template -> modified),
	];
}

// autoseo для ключей
// заполняем пустое значение датами из шаблона

if (
	!empty($seo -> keywords) &&
	empty($seo -> tags)
) {
	$seo -> tags = datasplit($seo -> keywords, ',');
	foreach ($seo -> tags as &$item) {
		$item = trim($item);
	}
}

// autoseo для копирайта
// заполняем пустое значение шаблоном с заголовком сайта
if (empty($seo -> copyright)) {
	$seo -> copyright = '&copy;' . date('Y') . ' ' . $lang -> data['title'];
}

// autoseo для фильтров
// помечаем триггер фильтров, чтобы, например, запрещать страницу на индексирование
if (
	!empty(objectGet('content', 'filtration')) ||
	objectGet('content', 'type') === 'all'
) {
	$seo -> filter = true;
}

// убираем настройки seo

unset(
	$seo -> content,
	$seo -> lang
);

//echo print_r($template, true) . '<hr>';
//echo print_r($content, true) . '<hr>';
//echo print_r($seo, true) . '<hr>';
//exit;

?>