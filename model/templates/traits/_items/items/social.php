<?php defined('isENGINE') or die; ?>
<?php
	foreach ($lang -> social as $key => $item) :
	if ( (is_string($item) && $item) || (is_array($item) && $item[0])) :
?>
<a href="<?= (is_array($item)) ? $item[0] : $item; ?>" target="_blank" class="social_item invert">
	<i class="social_icon <?= (is_array($item) && $item[1]) ? $item[1] : 'fab fa-' . $key; ?>" aria-hidden="true"></i>
</a>
<?php
	endif;
	endforeach;
?>