<?php

if (!defined('isENGINE')) { define('isENGINE', microtime(true)); }
if (!defined('CMS_MINIMUM_PHP')) { define('CMS_MINIMUM_PHP', '5.6.0'); }
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
if (!defined('DP')) { define('DP', '..' . DIRECTORY_SEPARATOR); }

//if (!defined('PATH_SITE')) { define('PATH_SITE', realpath($_SERVER['DOCUMENT_ROOT']) . DS); }
//if (!defined('PATH_BASE')) { define('PATH_BASE', realpath($_SERVER['DOCUMENT_ROOT'] . DS . '..') . DS); }

?>