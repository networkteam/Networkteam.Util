<?php
namespace Networkteam\Util\Converter;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class StaticRatesConverterService implements \Networkteam\Util\Converter\CurrencyConverterInterface {

	/**
	 * @var string
	 */
	protected $baseCurrency;

	/**
	 * @param $appId
	 * @param string $baseCurrency
	 * @param int $cacheLifetime
	 * @param string $baseUrl
	 */
	public function __construct($baseCurrency = 'EUR') {
		$this->baseCurrency = $baseCurrency;
		$this->rates = array(
			'EUR' => '1.00',
			'USD' => '1.50'
		);
	}

	/**
	 * @param string $baseCurrency
	 */
	public function setBaseCurrency($baseCurrency) {
		$this->baseCurrency = $baseCurrency;
	}

	/**
	 * @param string $currency
	 */
	public function getExchangeRate($currency) {
		if (isset($this->rates[$currency])) {
			return $this->rates[$currency];
		} else {
			throw new InvalidCurrencyException($currency . ' is not an available Currency', 1359207864);
		}
	}

	/**
	 * @param $amount
	 * @param $target
	 *
	 * @return mixed
	 */
	public function convert($amount, $target) {
		if ($target === $this->baseCurrency) {
			return $amount;
		}
		$baseValue = $amount / $this->getExchangeRate($this->baseCurrency);

		return $baseValue * $this->getExchangeRate($target);
	}

}
