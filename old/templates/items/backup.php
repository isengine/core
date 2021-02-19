<?php defined('isENGINE') or die;

if (DEFAULT_MODE === 'develop' && SECURE_BLOCKIP === 'developlist') {
	
	$backup = [
		objectProcess('system:backup'),
		null,/*'<a href="/' . DEFAULT_PROCESSOR . '/system/backup/?hash=' . crypting(time() + TIME_HOUR) . '&csrf=' . csrf(),*/
		'">BACKUP ',
		'</a>'
	];
	
	$backup[1] = '<a href="' . $backup[0]['action'] . $backup[0]['string'];
	
	/*
	$f = objectProcess('content:global');
	localRequestUrl($f['link'], $f['string'] . '&close=1&data=' . base64_encode(json_encode($this -> ratings)), 'post');
	unset($f);
	
	//localRequestUrl($uri -> site . DEFAULT_PROCESSOR . '/content/global/', 'hash=' . crypting(time() + TIME_HOUR) . '&csrf=' . csrf() . '&close=1&data=' . base64_encode(json_encode($this -> ratings)), 'post');
	*/
	
	echo "<!-- BACKUP SITE: uncommit for use -->\n<!--\n<hr>" . 
		$backup[1] . '' . $backup[2] . 'all' . $backup[3] . '<br>' . 
		$backup[1] . '&data[simple]' . $backup[2] . 'simple database and local folder' . $backup[3] . '<br>' . 
		$backup[1] . '&data[nolocal]' . $backup[2] . 'without local folder' . $backup[3] . '<br>' . 
		$backup[1] . '&data[nolocal]&data[simple]' . $backup[2] . 'simple database without local folder' . $backup[3] . 
	"<hr>\n-->";
	
	echo "<!-- BACKUP TEMPLATES: uncommit for use -->\n<!--\n<hr>";
	$f = localList(PATH_TEMPLATES, ['return' => 'folders']);
	foreach ($f as $i) {
		$i = str_replace(['/', DS], '', $i);
		echo $backup[1] . '&data[template]=1&data[templatename]=' . $i . $backup[2] . $i  . ' template' . $backup[3] . '<br>';
	}
	unset($f, $i);
	echo "<hr>\n-->";
	
}

?>