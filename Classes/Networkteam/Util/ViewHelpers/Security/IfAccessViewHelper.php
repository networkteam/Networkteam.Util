<?php
namespace Networkteam\Util\ViewHelpers\Security;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

class IfAccessViewHelper extends \TYPO3\Fluid\ViewHelpers\Security\IfAccessViewHelper {

	/**
	 * @param string $resource multiple Resources seperated by |
	 * @return string|void
	 */
	public function render($resource) {
		$accessGranted = FALSE;
		$resources = explode('|', $resource);

		foreach ((array)$resources as $resource) {
			if ($this->accessDecisionManager->hasAccessToResource(trim($resource))) {
				$accessGranted = TRUE;
				break;
			}
		}

		if ($accessGranted) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}
