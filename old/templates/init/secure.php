<?php defined('isENGINE') or die;

global $template;

// сюда писать проверку по ip на blacklist.whitelist.developlist
// сюда же пишем проверку на ограничение по get-запросу
// и по имени пользователя
// secure
//   type get/...list/user
//   list : []
// для запуска этой проверки, нужно добавить такую секцию в настройки шаблона

/*
	"secure" : {
		
		"type" : "getlist",
		"list" : [
			"user"
		]
		
		или
		
		"type" : "whitelist",
		"list" : [
			"127.0.0.1"
		]
		
	},
*/

$secure = $template -> settings -> secure;
$type = $secure['type'];
$list = $secure['list'];

init('functions', 'ip');
$ip = ipReal(); // или global $user; $user -> ip; но неизвестно, поможет ли это, когда user-ы выключены
$in_range = ipRange($ip, $list);

if (
	$type === 'blacklist' && $in_range ||
	$type === 'whitelist' && !$in_range ||
	$type === 'getlist' && (!objectIs($_GET) || !in_array(reset(array_keys($_GET)), $list))
) {
	error('404', true, 'error 404 from secure template -- block from ' . $type);
}

?>