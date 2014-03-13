<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;

class ObjectIdentifierViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

	/**
	 * @param object $object
	 * @return mixed
	 */
	public function render($object = NULL) {
		if ($object === NULL) {
			$object = $this->renderChildren();
			if ($object === NULL) {
				return '';
			}
		}
		return $this->persistenceManager->getIdentifierByObject($object);
	}
}
