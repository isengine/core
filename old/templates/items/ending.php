<?php defined('isENGINE') or die;

page('footer', 'html');

page('frontend', 'item');

if (in('options', 'scriptoptimise')) {
	page('scriptopt', 'item', false);
}

if (in('options', 'cookiesagree')) {
	page('cookies', 'item');
}

page('place', 'item', false);
page('tscript', 'item');
page('counters', 'item');

if (in('options', 'inspect')) {
	page('inspectend', 'item');
}

page('backup', 'item');

?>

</body>
</html>