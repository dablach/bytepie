<?php

// var_dump(\OC::$server->getURLGenerator()->linkToRoute('bytepie.graph.index')); exit;

\OC::$server->getNavigationManager()->add(function() {
	$urlGenerator = \OC::$server->getURLGenerator();
	return [
		'id' => 'bytepie',
		'order' => 10,
		'href' => $urlGenerator->linkToRoute('bytepie.graph.index'),
		'icon' => $urlGenerator->imagePath('bytepie','app.svg'),
		'name' => 'Byte Pie',
	];
});
