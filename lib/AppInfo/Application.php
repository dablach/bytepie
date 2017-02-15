<?php
namespace OCA\BytePie\AppInfo;

class Application extends \OCP\AppFramework\App
{
	public function __construct(array $urlParams = array()) {
		parent::__construct('bytepie',$urlParams);

		$container = $this->getContainer();

		$container->registerService('OCA\BytePie\Controller\GraphController',function($c) {
			return new \OCA\BytePie\Controller\GraphController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('OCP\Files\IRootFolder'),
				$c->query('OCP\IUserSession'),
				$c->query('ServerContainer')->getURLGenerator()
			);
		});
	}
}
