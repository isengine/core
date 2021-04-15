<?php defined('isENGINE') or die;

header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
header('Content-Type: text/html; charset=UTF-8');

?><!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
	<meta name="robots" content="noindex, nofollow" />
	<title>isENGINE system info</title>
</head>
<body>

<div>
<center>

	<p><strong>System Info</strong></p>
	<p>
		Platform: isENGINE
		<br>
		Version: <?= isENGINE; ?>
		<br>
		Server: <?= $_SERVER['SERVER_SOFTWARE']; ?>
		<br>
		Date: <?= date('d.m.Y', filemtime($_SERVER['SCRIPT_FILENAME'])); ?>
	</p>
	<p>project on github:<br><a href="https://github.com/isengine/" target="_blank">https://github.com/isengine/</a></p>

</center>
</div>

</body>
</html><?php exit; ?>