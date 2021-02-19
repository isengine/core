<?php

// Рабочее пространство имен

namespace is;

use is\Helpers\Ip;

// Check ip block
// Проверка блокировки по ip

// Здесь практически не используются никакие функции фреймворка,
// чтобы максимально облегчить код
// для снижения нагрузки, в том числе при DDoS-атаке

// на данный момент нагузка составляет в среднем 500 Kb (475/525)
// для RAM 1 GB это около 2000 одновременных запросов
// на локальной машине время обработки этих запросов составляет 0.700 сек

$mode = file_get_contents( DR . 'config' . DS . 'ip.mode.ini' );
$list = file_get_contents( DR . 'config' . DS . 'ip.' . $mode . '.ini' );
$list = json_decode($list, true);
$block = null;

if (!empty($list)) {
	
	$ip = Ip::real();
	$in_range = Ip::range($ip, $list);
	
	if (
		($mode === 'blacklist' && $in_range) ||
		($mode === 'whitelist' && !$in_range) ||
		($mode === 'develop' && !$in_range)
	) {
		$block = true;
	}
	
}

if ($block) {
	if ($mode === 'develop') {
		echo 'System update. Wait...';
	} elseif ($mode === 'whitelist') {
		echo 'Your ip not in whitelist';
	} else {
		echo 'Your ip in blacklist';
	}
	exit;
}

unset($mode, $list, $list, $block);

?>