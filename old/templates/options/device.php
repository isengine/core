<?php defined('isENGINE') or die;

global $template;

/*
if (cookie('device', true)) {
	
	$template -> device = json_decode(cookie('device', true));
	
} else
*/

if (
	in('libraries', 'mobiledetect:system') ||
	in('libraries', 'Mobile-Detect:serbanghita')
) {
	
	$template -> device = (object) [
		'type' => null,
		'os' => null,
		'screen' => null
	];
	
	$mobiledetect = new Mobile_Detect;
	
	$template -> device -> type = ($mobiledetect->isMobile() ? ($mobiledetect->isTablet() ? 'tablet' : 'mobile') : 'desktop');
	
	if ( $mobiledetect->isWindowsPhoneOS() ) {
		$template -> device -> os = 'windowsphone';
	} elseif ( $mobiledetect->isiOS() ) {
		$template -> device -> os = 'ios';
	} elseif ( $mobiledetect->isAndroidOS() ) {
		$template -> device -> os = 'android';
	}
	
	unset($mobiledetect);
	
	//cookie('device', json_encode($template -> device));
	
}

?>