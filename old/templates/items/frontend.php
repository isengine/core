<?php defined('isENGINE') or die;

if (
	!empty($administrator) &&
	empty($administrator -> in) &&
	in_array('frontend', $administrator -> options) &&
	file_exists($administrator -> path -> base . DS . 'html' . DS . 'frontend' . DS . $administrator -> path -> curr)
) {
	require_once $administrator -> path -> base . DS . 'html' . DS . 'frontend' . DS . $administrator -> path -> curr;
}

?>