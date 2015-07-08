<?php
namespace Networkteam\Util\ViewHelpers\Format;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

class PrintfViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

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
