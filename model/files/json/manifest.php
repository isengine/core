<?php

namespace is\Model\Files\Json;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Model\Parents\Data;
use is\Model\Components\Router;
use is\Model\Components\State;
use is\Model\Components\Uri;
use is\Model\Views\View;
use is\Model\Files\Master;

class Manifest extends Master {
	
	public function launch() {
		
		$view = View::getInstance();
		
		$webapp = $view -> get('state|settings:webapp');
		$icons = $view -> get('icon|data');
		
		$json = [
			'name' => html_entity_decode(!empty($webapp['name']) ? $webapp['name'] : $view -> get('lang|title')),
			'short_name' => html_entity_decode(!empty($webapp['short_name']) ? $webapp['short_name'] : $view -> get('lang|title')),
			'description' => html_entity_decode(!empty($webapp['description']) ? $webapp['description'] : $view -> get('lang|description')),
			'theme_color' => $webapp['color'],
			'background_color' => $webapp['background'],
			'display' => !empty($webapp['display']) ? $webapp['display'] : 'standalone',
			'start_url' => !empty($webapp['start_url']) ? $webapp['start_url'] : '/'
		];
		
		foreach (['splashscreen', 'webapp'] as $key) {
			if (!empty($icons[$key])) {
				foreach ($icons[$key]['sizes'] as $item) {
					$item = strpos($item, ':') !== false ? str_replace(':', 'x', $item) : $item . 'x' . $item;
					$json['icons'][] = [
						'src' => $view -> get('state|domain') . $icons['settings']['path'] . '/' . $icons[$key]['name'] . '-' . $item . '.png',
						'type' => 'image/png',
						'sizes' => $item
					];
				}
				unset($item);
			}
		}
		
		$this -> addBuffer( json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) );
		
		Sessions::setHeader(['Content-type' => 'application/json; charset=utf-8']);
		
		unset($json, $webapp, $icons);
		
	}
	
}

?>