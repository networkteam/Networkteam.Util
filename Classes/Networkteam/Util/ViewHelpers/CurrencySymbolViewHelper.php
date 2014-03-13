<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class CurrencySymbolViewHelper extends AbstractViewHelper {

	public function render($currency) {
		$currencyName = strtoupper($currency);
		switch ($currencyName) {
			case 'USD':
				$symbol = '$';
				break;
			default:
			case 'EUR':
				$symbol = '€';
				break;
		}

		return $symbol;
	}

}
