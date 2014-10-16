<?php
namespace Networkteam\Util\Resource;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;

class ResourceLocator {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Resource\Publishing\ResourcePublisher
	 */
	protected $resourcePublisher;

	/**
	 * @var \TYPO3\Flow\Mvc\Controller\ControllerContext
	 */
	protected $context;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\I18n\Service
	 */
	protected $i18nService;

	/**
	 * @param string $path
	 * @param string $package
	 * @param \TYPO3\Flow\Resource\Resource $resource
	 * @param boolean $localize
	 * @param boolean $cacheBuster
	 * @return string
	 * @throws \TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException
	 */
	public function getResourceUri($path = NULL, $package = NULL, \TYPO3\Flow\Resource\Resource $resource = NULL, $localize = TRUE, $appendCacheBuster = TRUE) {
		$cacheBuster = '';
		if ($resource !== NULL) {
			$uri = $this->resourcePublisher->getPersistentResourceWebUri($resource);
			if ($uri === FALSE) {
				$uri = $this->resourcePublisher->getStaticResourcesWebBaseUri() . 'BrokenResource';
			}
		} else {
			if ($path === NULL) {
				throw new \TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException('The ResourceViewHelper did neither contain a valuable "resource" nor "path" argument.', 1353512742);
			}
			if ($package === NULL) {
				$package = $this->context->getRequest()->getControllerPackageKey();
			}
			if (strpos($path, 'resource://') === 0) {
				$matches = array();
				if (preg_match('#^resource://([^/]+)/Public/(.*)#', $path, $matches) === 1) {
					$package = $matches[1];
					$path = $matches[2];
				} else {
					throw new \TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException(sprintf('The path "%s" which was given to the ResourceViewHelper must point to a public resource.', $path), 1353512639);
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

				$resourcePath = 'resource://' . $package . '/Public/' . $path;
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

			$uri = $this->resourcePublisher->getStaticResourcesWebBaseUri() . 'Packages/' . $package . '/' . $path . $cacheBuster;
		}

		return $uri;
	}

	/**
	 * @param \TYPO3\Flow\Mvc\Controller\ControllerContext $context
	 */
	public function setContext(\TYPO3\Flow\Mvc\Controller\ControllerContext $context) {
		$this->context = $context;
	}
}
