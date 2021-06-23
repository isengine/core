<?php

namespace is\Masters\Files\Txt;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Parents\Data;
use is\Components\Router;
use is\Components\State;
use is\Components\Config;
use is\Components\Uri;
use is\Masters\View;
use is\Masters\Files\Master;

class Robots extends Master {
	
	public function launch() {
		
		$view = View::getInstance();
		$config = Config::getInstance();
		
		Sessions::setHeader(['Content-type' => 'text/plain; charset=utf-8']);
		
		// сейчас нет учета системных папок, т.к. они находятся "снаружи"
		// но вообще-то их тоже стоит считать, например собрав real адреса
		// из config -> get('path') и сравнив с адресом public папки сайта
		// и получившийся адрес запрещать в disallow
		// можно еще ставить allow на url:assets, т.к. он может находиться
		// внутри какой-нибудь другой папки
		
?>
# For more information about the robots.txt standard, see:
# http://www.robotstxt.org/orig.html
# For syntax checking, see:
# http://tool.motoricerca.info/robots-checker.phtml

User-agent: *
<?php
		if ($config -> get('develop:enable') && $config -> get('develop:noindex')) {
?>
Disallow: /
<?php
		} else {
?>	
Disallow: /cgi-bin
Disallow: /<?= $config -> get('api:name'); ?>/
Disallow: *?
Sitemap: <?= $view -> get('state|domain'); ?>sitemap.xml
<?php
		}
?>
Host: <?= $view -> get('state|domain'); ?>	
Crawl-delay: 10
<?php
	}
}
?>