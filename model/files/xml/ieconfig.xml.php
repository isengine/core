<?php defined('isENGINE') or die;

// initial

init('init', 'fast');

// webapp

$template = dbUse('templates:' . (!empty($_GET['template']) ? $_GET['template'] : 'default'), 'select', true);
if (!empty($template)) {
	$template = array_shift($template);
	$webapp = $template['webapp'];
} else {
	$webapp = null;
}
unset($template);

// icons

$icons = dbUse('icons', 'select', true);

//echo '<pre style="font-size: 10px; line-height: 0.8em;">' . print_r($webapp, 1) . '</pre><hr><br>';
//echo '<pre style="font-size: 10px; line-height: 0.8em;">' . print_r($lang, 1) . '</pre><hr><br>';
//echo '<pre style="font-size: 10px; line-height: 0.8em;">' . print_r($icons, 1) . '</pre><hr><br>';

header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
header('Content-type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<browserconfig>
	<msapplication>
		<tile>
			<?php
				foreach ($icons['msapplication']['sizes'] as $item) :
					$size = dataParse($item);
					if (empty($size[1])) {
						$size[1] = $size[0];
					}
					$item = $size[0] . 'x' . $size[1];
					$tag = $item === '144x144' ? true : false;
					$type = $size[1] === $size[0] ? 'square' : 'wide';
					$path = URL_LOCAL . $icons['settings']['path'] . '/' . $icons['msapplication']['name'] . '-' . $item . '.png';
			?>
			<<?= $tag ? 'TileImage' : $type . $item . 'logo'; ?> src="<?= $path; ?>" />
			<?php
				endforeach;
				unset($item, $path, $type, $size, $tag);
			?>
			<TileColor><?= $webapp['color']; ?></TileColor>
		</tile>
		<?/*
		<notification>
			<polling-uri src="/notifications/contoso1.xml"/> // ссылка на новости или статьи, разрешено до 5 элементов polling-uri2 ... polling-uri5
			<frequency>30</frequency> // обновление в минутах: 30, 60, 360, 720 или 1440 (в часах это 0.5, 1, 6, 12 и 24)
			<cycle>1</cycle>
		</notification>
		*/?>
	</msapplication>
</browserconfig>
<?php

unset($webapp, $icons);
exit;

?>