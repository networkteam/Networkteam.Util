<?php
namespace Networkteam\Util\Resource;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\ResourceManager;
use TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException;

class ResourceLocator {

	/**
	 * @Flow\Inject
	 * @var ResourceManager
	 */
	protected $resourceManager;

	/**
	 * @var \Neos\Flow\Mvc\Controller\ControllerContext
	 */
	protected $controllerContext;

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\I18n\Service
	 */
	protected $i18nService;

	/**
	 * @param string $path
	 * @param string $package
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @param boolean $localize
	 * @param boolean $appendCacheBuster
	 * @return string
	 * @throws \TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException
	 */
	public function getResourceUri($path = NULL, $package = NULL, \Neos\Flow\ResourceManagement\PersistentResource $resource = NULL, $localize = TRUE, $appendCacheBuster = TRUE) {
		$cacheBuster = '';

		if ($resource !== NULL) {
			$uri = $this->resourceManager->getPublicPersistentResourceUri($resource);
			if ($uri === FALSE) {
				$uri = '404-Resource-Not-Found';
			}
		} else {
			if ($path === NULL) {
				throw new InvalidVariableException('The ResourceViewHelper did neither contain a valuable "resource" nor "path" argument.', 1353512742);
			}
			if ($package === NULL) {
				$package = $this->controllerContext->getRequest()->getControllerPackageKey();
			}
			if (strpos($path, 'resource://') === 0) {
				$matches = array();
				if (preg_match('#^resource://([^/]+)/Public/(.*)#', $path, $matches) === 1) {
					$package = $matches[1];
					$path = $matches[2];
				} else {
					throw new InvalidVariableException(sprintf('The path "%s" which was given to the ResourceViewHelper must point to a public resource.', $path), 1353512639);
				}
			}
			if ($localize === TRUE) {
				$resourcePath = 'resource://' . $package . '/Public/' . $path;
				$localizedResourcePathData = $this->i18nService->getLocalizedFilename($resourcePath);
				$matches = array();
				if (preg_match('#resource://([^/]+)/Public/(.*)#', current($localizedResourcePathData), $matches) === 1) {
					$package = $matches[1];
					$path = $matches[2];
				}
			}

			if ($appendCacheBuster === TRUE) {
				$resourcePath = 'resource://' . $package . '/Public/' . $path;
				if (is_file($resourcePath)) {
					$resourceModificationTimestamp = filemtime($resourcePath);
					if ($resourceModificationTimestamp !== FALSE) {
						$cacheBuster = '?' . $resourceModificationTimestamp;
					}
				}
			}

			$uri = $this->resourceManager->getPublicPackageResourceUri($package, $path) . $cacheBuster;
		}
		return $uri;
	}

	/**
	 * @param \Neos\Flow\Mvc\Controller\ControllerContext $context
	 */
	public function setContext(\Neos\Flow\Mvc\Controller\ControllerContext $context) {
		$this->controllerContext = $context;
	}
}
