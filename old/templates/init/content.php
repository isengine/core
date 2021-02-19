<?php defined('isENGINE') or die;

global $template;
global $content;

if ($template -> page['type'] === 'content') {
	
	$content = new Content();
	//$content = new Content(':seance');
	//$content = new Content(':seance:list');
	//$content = new Content(':seance:all');
	//$content = new Content(['dino_planet:astronomija', 'seance', 'list']);
	//$content = new Content(['dino_planet:astronomija', 'seance']);
	//$content = new Content('dino_planet:seance');
	//$content = new Content('dino_planet:seance:all');
	$content -> settings();
	$content -> read();
	//$content -> display($content -> parent);
	//unset($content); // удалить объект можно только так
	//print_r($content);
	
	if ($content -> type === 'alone' && !set($content -> name)) {
		error('404', false, 'error 404 from template -- not init content');
	}
	
}

?>