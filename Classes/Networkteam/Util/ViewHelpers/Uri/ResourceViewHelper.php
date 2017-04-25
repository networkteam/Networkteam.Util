<?php
namespace Networkteam\Util\ViewHelpers\Uri;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;

class ResourceViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * @var \Networkteam\Util\Resource\ResourceLocator
	 * @Flow\Inject
	 */
	protected $resourceLocator;

	/**
	 * Render the URI to the resource. The filename is used from child content.
	 *
	 * @param string $path The location of the resource, can be either a path relative to the Public resource directory of the package or a resource://... URI
	 * @param string $package Target package key. If not set, the current package key will be used
	 * @param \TYPO3\Flow\ResourceManagement\PersistentResource $resource If specified, this resource object is used instead of the path and package information
	 * @param boolean $localize Whether resource localization should be attempted or not
	 * @param boolean $cacheBuster
	 * @return string The absolute URI to the resource
	 * @throws \TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException
	 * @api
	 */
	public function render($path = NULL, $package = NULL, \TYPO3\Flow\ResourceManagement\PersistentResource $resource = NULL, $localize = TRUE, $cacheBuster = TRUE) {
		$this->resourceLocator->setContext($this->controllerContext);
		return $this->resourceLocator->getResourceUri($path, $package, $resource, $localize, $cacheBuster);
	}
}
