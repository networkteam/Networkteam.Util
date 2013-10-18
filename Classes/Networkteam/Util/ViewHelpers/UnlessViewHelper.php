<?php
namespace Networkteam\Util\ViewHelpers;
/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/
class UnlessViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * renders <f:then> child if $condition is true, otherwise renders <f:else> child.
	 *
	 * @param boolean $condition View helper condition
	 * @return string the rendered string
	 * @api
	 */
	public function render($condition) {
		if (!$condition) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}
?>
