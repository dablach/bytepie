<?php
namespace OCA\BytePie\Controller;

class GraphController extends \OCP\AppFramework\Controller
{
	protected $rootFolder;
	protected $userSession;
	protected $urlGenerator;

	public function __construct($appName,\OCP\IRequest $request,\OCP\Files\IRootFolder $rootFolder,\OCP\IUserSession $userSession,\OCP\IURLGenerator $urlGenerator) {
		parent::__construct($appName,$request);
		$this->rootFolder = $rootFolder;
		$this->userSession = $userSession;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index($zoom,$path) {
		$user = $this->userSession->getUser();
		$uid = $this->userSession->getUser()->getUID();
		$root = $this->rootFolder->getUserFolder($uid);
		return new \OCP\AppFramework\Http\TemplateResponse($this->appName,'graph',[
			'folder' => $root->get($path),
			'root' => $root,
			'zoom' => $zoom,
			'urlGenerator' => $this->urlGenerator,
		]);
	}
}
