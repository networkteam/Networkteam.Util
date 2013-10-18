<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class CurrencyConversionViewHelper extends AbstractViewHelper {

	/**
	 * @var \Networkteam\Util\Converter\CurrencyConverterInterface
	 * @Flow\Inject
	 */
	protected $currencyConverter;

	/**
	 * @param integer $price Price in cents
	 * @param string $target Target currency code
	 * @param string $baseCurrency Base currency code
	 *
	 * @return float
	 */
	public function render($price, $target, $baseCurrency) {
		$decimalPrice = $price / 100;
		$this->currencyConverter->setBaseCurrency($baseCurrency);
		return sprintf('%0.2F', round($this->currencyConverter->convert($decimalPrice, $target), 2));
	}
}

?>