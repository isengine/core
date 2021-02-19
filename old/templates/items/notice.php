<?php defined('isENGINE') or die;

if (defined('isCOOKIE') && !isCOOKIE) {
	echo lang('errors:cookie');
}

?>
<noscript><?= lang('errors:script'); ?></noscript>