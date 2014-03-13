<?php
namespace Networkteam\Util\ViewHelpers\Format;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class LowercaseViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @param string $value
	 * @return string
	 */
	public function render() {
		return strtolower($this->renderChildren());
	}
}
