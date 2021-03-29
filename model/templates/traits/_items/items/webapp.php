<?php defined('isENGINE') or die;

global $uri;
global $template;

if (objectIs($template -> settings -> webapp)) :

$webapp = &$template -> settings -> webapp;

?>

<!-- Web Application -->

<?php if (!empty($webapp['msapplication'])) : ?>

<meta name="msapplication-tooltip" content="<?= lang('title') . ' ' . $uri -> host; ?>" />
<meta name="msapplication-starturl" content="<?= $uri -> site; ?>" />

<?php if ($webapp['msapplication'] === true) : ?>

<meta name="msapplication-TileColor" content="<?= $webapp['color']; ?>" />
<?php
	
	$icons = dbUse('icons', 'select', true);
	$path = URL_LOCAL . $icons['settings']['path'] . '/';
	$print = null;
	
	$item = [
		'name' => $icons['msapplication']['name'],
		'metaname' => null,
		'size' => null,
		'height' => null
	];
	
	if (objectIs($icons['msapplication']['sizes'])) {
	foreach ($icons['msapplication']['sizes'] as $i) {
		
		$item['size'] = dataParse($i, true);
		
		if (empty($item['size'][1])) {
			$item['size'][1] = $item['size'][0];
		}
		
		if ($item['size'][0] == '144') {
			$item['metaname'] = 'TileImage';
		} elseif ($item['size'][0] !== $item['size'][1]) {
			$item['metaname'] = 'wide' . $item['size'][0] . 'x' . $item['size'][1] . 'logo';
		} else {
			$item['metaname'] = 'square' . $item['size'][0] . 'x' . $item['size'][1] . 'logo';
		}
		
		$print .= '<meta name="msapplication-' . $item['metaname'] . '" content="' . $path . $item['name'] . '-' . $item['size'][0] . 'x' . $item['size'][1] . '.png">';
		
	}
	}
	
	echo $print;
	
	unset($i, $item, $print, $path, $icons);
	
?>

<?php else : ?>
<meta name="msapplication-config" content="<?= $uri -> site; ?>ieconfig.xml" />
<?php endif; ?>

<?php endif; ?>

<?php if (!empty($webapp['apple'])) : ?>

<meta name="apple-mobile-web-app-title" content="<?= lang('title'); ?>">
<meta name="apple-mobile-web-app-capable" content="yes">

<?php endif; ?>

<meta name="application-name" content="<?= lang('title'); ?>">
<meta name="theme-color" content="<?= $webapp['color']; ?>" />

<link rel="manifest" href="<?= $uri -> site; ?>manifest.json<?php
	
	if (!empty($webapp['settings'])) {
		$s = [];
		if ($template -> name !== 'default') {
			$s['template'] = $template -> name;
		}
		if ($lang -> lang !== DEFAULT_LANG) {
			$s['lang'] = $lang -> lang;
		}
		if (objectIs($s)) {
			echo '?' . objectToString($s, '&', '=');
		}
		unset($s);
	};
	
?>">

<?php if (!empty($webapp['serviceworker'])) : ?>
<?php if ($webapp['serviceworker'] === true) : ?>
<script type="module">
/*
 This code uses the pwa-update web component https://github.com/pwa-builder/pwa-update to register your service worker,
 tell the user when there is an update available and let the user know when your PWA is ready to use offline.
*/
import 'https://cdn.jsdelivr.net/npm/@pwabuilder/pwaupdate';
const el = document.createElement('pwa-update');
document.body.appendChild(el);
<?php else : ?>
<script<?= in('options', 'oldbrowsers') ? 'type="text/javascript"' : null; ?>>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {  
    navigator.serviceWorker.register('/<?= empty($webapp['custompath']) ? $webapp['serviceworker'] : 'serviceworker.script'; ?>').then(
      function(registration) {
        // Registration was successful
        console.log('ServiceWorker registration successful with scope: ', registration.scope); },
      function(err) {
        // registration failed
        console.log('ServiceWorker registration failed: ', err);
      });
  });
}
<?php endif; ?>
</script>
<?php endif; ?>

<?php
unset($webapp);
endif;
?>