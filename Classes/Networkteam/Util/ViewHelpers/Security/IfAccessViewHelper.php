<?php
namespace Networkteam\Util\ViewHelpers\Security;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

class IfAccessViewHelper extends \Neos\FluidAdaptor\ViewHelpers\Security\IfAccessViewHelper {

	/**
	 * Check if any of the given privilege targets is granted
	 *
	 * @param string $privilegeTarget One or multiple privilege targets (separated by '|')
	 * @param array $parameters
	 * @return string
	 */
	public function render($privilegeTarget, array $parameters = array()) {
		$privilegeTargets = explode('|', $privilegeTarget);

		foreach ($privilegeTargets as $privilegeTarget) {
			if ($this->privilegeManager->isPrivilegeTargetGranted($privilegeTarget, $parameters)) {
				return $this->renderThenChild();
			}
		}

		return $this->renderElseChild();
	}
}
