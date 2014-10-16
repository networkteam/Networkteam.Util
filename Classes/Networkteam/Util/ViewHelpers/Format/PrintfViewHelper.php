<?php
namespace Networkteam\Util\ViewHelpers\Format;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

class PrintfViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param array $arguments
	 * @param string $format
	 * @return int
	 */
	public function render(array $arguments, $format = NULL) {
		if ($format === NULL) {
			$format = $this->renderChildren();
		}

		return vsprintf($format, $arguments);
	}
}
