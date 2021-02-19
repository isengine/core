<?php defined('isPROCESS') or die;

$data = $process -> data;
$print = '';

//echo print_r($data, true) . '<br><br>';

if (set($data['filter'])) {
	foreach ($data['filter'] as $key => $item) {
		
		if (!set($item)) {
			continue;
		} elseif (
			$data['types'][$key] === 'numeric' ||
			$data['types'][$key] === 'range' ||
			$data['types'][$key] === 'range_bootstrap' ||
			$data['types'][$key] === 'range_jqueryui'
		) {
			$item = $item[0] . '_' . $item[1];
		} elseif (objectIs($item)) {
			if (
				$data['types'][$key] === 'and' ||
				$data['types'][$key] === 'buttons' ||
				$data['types'][$key] === 'buttons_after'
			) {
				$item = ':+' . objectToString($item, ':+');
			} else {
				$item = objectToString($item, ':');
			}
		}
		
		$print .= $key . '/' . clear($item, 'urlencode') . '/';
		
	}
	unset($key, $item);
}

if (!empty($data['items'])) {
	$print .= 'items/' . $data['items'] . '/';
}

$print = '/' . $process -> data['target'] . (!empty($print) && empty($data['reset']) ? 'filter/' . $print : null);

//echo '<a href="' . $print . '">' . $print . '</a>';

reload($print);
//header('Location: ' . $print);
//exit;

?>