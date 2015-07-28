<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class DecimalPriceViewHelper extends AbstractViewHelper {

	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * renders the children and divides the result by 100
	 * @return float
	 */
	public function render() {
		$price = $this->renderChildren();
		return $price / 100;
	}
}
