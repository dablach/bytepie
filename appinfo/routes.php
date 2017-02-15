<?php
namespace OCA\BytePie\AppInfo;

$application = new Application();
$application->registerRoutes($this,[
	'routes' => [
		['name' => 'graph#index','url' => '/{zoom}/{path}','verb' => 'GET','requirements' => ['zoom' => '[0-9]+','path' => '.*'],'defaults' => ['zoom' => '5','path' => '']],
	],
]);
