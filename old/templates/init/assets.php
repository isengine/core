<?php defined('isENGINE') or die;

global $template;

if (objectIs($template -> settings -> assets)) {
	
	$assets = [];
	
	foreach ($template -> settings -> assets as $key => $item) {
		if (objectIs($item)) {
			if ($key === 'fonts') {
				
				$assets['headopen'][$key] = [
					'list' => $i,
					'langs' => [],
				];
				
				foreach ($item as $k => $i) {
					$i = datasplit($i, ':', false);
					
					if (objectIs($i) && !empty($i[0])) {
						
						if (strpos($i[0], ' ') !== false) {
							$i[0] = str_replace(' ', '+', $i[0]);
						}
						
						if (!empty($i[1])) {
							$i[1] = str_replace([' ', '.'], ',', $i[1]);
						}
						
						if (!empty($i[2])) {
							$assets['headopen'][$key]['langs'] = array_merge($assets['headopen'][$key]['langs'], datasplit($i[2]));
							unset($i[2]);
						}
						
						$assets['headopen'][$key]['list'][] = $i;
						
					}
				}
				
				$assets['headopen'][$key]['langs'] = objectClear($assets['headopen'][$key]['langs'], false, true);
				
			} else {
				
				foreach ($item as $i) {
					
					$i = dataParse($i);
					
					if (empty($i[1])) {
						$i[1] = 'headopen';
					}
					$assets[$i[1]][$key][] = $i[0];
					
				}
				
			}
		}
	}
	
	//print_r($template -> settings -> assets);
	//print_r($assets);
	
	$template -> settings -> assets = $assets;
	
	unset($assets, $key, $item, $i);
	
}

?>