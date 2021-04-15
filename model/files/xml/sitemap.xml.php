<?php defined('isENGINE') or die;

// инициализация всех необходимых компонентов

init('init', 'fast');
init('class', 'content');
//require_once PATH_CORE . 'classes' . DS . 'content' . DS . 'content.php';

// подготовка переменных

global $lang;
global $structure;

$data = [
	'base' => [],
	'links' => [
		'inner' => [],
		'pages' => [],
		'content' => []
	],
	'dates' => [
	]
];

$counter = 0;

$settings = [
	'lastmod' => DATE_ATOM,
	'changefreq' => [
		'base' => 'daily',
		'inner' => 'daily',
		'pages' => 'weekly',
		'content' => 'monthly'
	],
	'priority' => [
		'base' => '1',
		'inner' => '0.8',
		'pages' => '0.4',
		'content' => '0.6'
	],
	'allow' => [
		'base' => true,
		'inner' => true,
		'pages' => true,
		'content' => true,
		'templates' => true
	],
	'counter' => 50000
];

$contents = [];

// читаем пользовательские настройки карты

$sets = dbUse('seo:default', 'select', ['return' => 'alone:data']);
if (objectIs($sets) && objectIs($sets['sitemap'])) {
	//$settings = objectMerge($settings, $sets['sitemap'], 'replace');
	$settings = array_replace_recursive($settings, $sets['sitemap']);
}
unset($sets);

//print_r($settings);

// часть первая
// формируем базовые ссылки, т.е. языковые

if (!empty($lang) && objectIs($lang -> list) && !empty($settings['allow']['base'])) {
	
	foreach ($lang -> list as $item) {
		
		if (
			DEFAULT_LANG === true ||
			DEFAULT_LANG === $item
		) {
			$uri -> path -> base = '/';
		} else {
			$uri -> path -> base = '/' . $item . '/';
		}
		
		$data['base'][] = $uri -> scheme . '://' . $uri -> host . $uri -> path -> base;
		
		if (DEFAULT_LANG === true) { break; }
		
	}
	
	unset($item);
	
} else {
	$data['base'][] = $uri -> scheme . '://' . $uri -> host . $uri -> path -> base;
}

// часть вторая
// формируем ссылки страниц из структуры

function sitemap_structureRemap($target, &$array, &$contents, $settings) {
	
	foreach ($target as $key => $item) {
		
		$i = dataParse($key);
		
		if (
			!empty($settings['deny']['pages']) &&
			is_array($settings['deny']['pages']) &&
			in_array($i[1], $settings['deny']['pages'])
		) {
			continue;
		}
		
		if (
			empty($i[2]) ||
			$i[2] === 'params' ||
			$i[2] === 'content'
		) {
			$i[3] = str_replace('.', '/', $i[3]);
			$array[] = $i[3];
			
			if ($i[2] === 'content') {
				$contents[] = [
					'name' => $i[1],
					'link' => $i[3]
				];
			}
			
		}
		
		if (objectIs($item)) {
			sitemap_structureRemap($item, $array, $contents, $settings);
		}
		
		unset($i);
		
	}
	
	unset($key, $item);
	
}

if (!empty($settings['allow']['inner'])) {
	sitemap_structureRemap($structure, $data['links']['inner'], $contents, $settings);
}

// часть третья
// формируем ссылки из контента

foreach ($contents as &$item) {
	
	// часть третья - раздел первый
	// читаем контент
	
	$content = new Content(':' . $item['name'] . ':list');
	$content -> settings();
	$content -> read();
	
	if (objectIs($content -> data)) {
		
		// часть третья - раздел второй
		// вычисляем число страниц
		
		$pages = null;
		$sets = dbUse('modules:' . $item['name'], 'select', ['allow' => 'parent:content', 'return' => 'alone:data']);
		
		if (empty($sets)) {
			$sets = dbUse('modules:default', 'select', ['allow' => 'parent:content', 'return' => 'alone:data']);
		}
		
		if (!empty($sets['navigation']['enable']) && !empty($settings['allow']['pages'])) {
			
			$sets = $sets['display'];
			if (!empty($sets['count']['list']) && is_numeric($sets['count']['list']) && $sets['count']['list'] > 0) {
				$pages = ceil((count($content -> data) - $sets['skip']['list']) / $sets['count']['list']);
			}
			
			if ($pages > 1) {
				for ($i = 2; $i <= $pages; $i++) {
					$data['links']['pages'][] = $item['link'] . 'page/' . $i . '/';
				}
				unset($i);
			}
			
		}
		
		unset($pages);
		unset($sets);
		
		// часть третья - раздел третий
		// добавляем ссылки на отображаемый контент
		
		if (!empty($settings['allow']['content'])) {
			foreach ($content -> data as $i) {
				if (set($i['name'])) {
					$data['links']['content'][] = $item['link'] . $i['name'] . '/';
					$data['dates'][] = $i['mtime'];
				}
			}
			
			unset($i);
		}
		
	}
	
	//print_r($content);
	
	unset($content);
	
}

unset($key, $item, $contents);

// часть последняя
// формируем вывод

foreach ($data['links'] as &$item) {
	$item = array_unique($item);
}
unset($item);

//echo "===\r\n" . print_r($data['links'], true) . "===\r\n\r\n";

//print_r($contents);
//print_r($uri);
//print_r($lang);
//print_r($structure);

// теперь просто выводим карту сайта

$sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\r\n";

if (objectIs($data['base'])) {
	foreach ($data['base'] as $link) {
		
		$sitemap .= "<url>\r\n\t<loc>" . $link . "</loc>";
		if (!empty($settings['lastmod'])) {
			$sitemap .= "\r\n\t<lastmod>" . date($settings['lastmod']) . "</lastmod>";
		}
		if (!empty($settings['changefreq']['base'])) {
			$sitemap .= "\r\n\t<changefreq>" . $settings['changefreq']['base'] . "</changefreq>";
		}
		$sitemap .= "\r\n\t<priority>" . $settings['priority']['base'] . "</priority>\r\n</url>\r\n";
		
		if (
			objectIs($data['links']) &&
			(int) $counter < (int) $settings['counter']
		) {
			foreach ($data['links'] as $key => $item) {
				
				if (
					objectIs($item) &&
					(int) $counter < (int) $settings['counter']
				) {
					foreach ($item as $k => $i) {
						
						$sitemap .= "<url>\r\n\t<loc>" . $link . $i . "</loc>";
						if (!empty($settings['lastmod'])) {
							$sitemap .= "\r\n\t<lastmod>" . date($settings['lastmod'], $key === 'content' ? $data['dates'][$k] : time()) . "</lastmod>";
						}
						if (!empty($settings['changefreq'][$key])) {
							$sitemap .= "\r\n\t<changefreq>" . $settings['changefreq'][$key] . "</changefreq>";
						}
						$sitemap .= "\r\n\t<priority>" . $settings['priority'][$key] . "</priority>\r\n</url>\r\n";
						
						$counter++;
						if ((int) $counter >= (int) $settings['counter']) {
							break;
						}
						
					}
					unset($k, $i);
				}
			}
		}
		
		unset($key, $item);
		
	}
}

unset($data, $settings, $counter);

$sitemap .= "</urlset>";

header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
header('Content-type: application/xml; charset=utf-8');
echo $sitemap;

exit;

?>