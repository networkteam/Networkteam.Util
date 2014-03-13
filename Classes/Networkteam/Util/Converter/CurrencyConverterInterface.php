<?php
namespace Networkteam\Util\Converter;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

interface CurrencyConverterInterface {

	/**
	 * Convert amount given into currency given
	 * @param float $amount Decimal price
	 * @param string $targetCurrency
	 *
	 * @return mixed
	 */
	public function convert($amount, $targetCurrency);

	/**
	 * Get exchange date for given currency
	 * @param $currency
	 *
	 * @return mixed
	 */
	public function getExchangeRate($currency);

	/**
	 * Sets the currency to convert from
	 * @param $currency
	 *
	 * @return void
	 */
	public function setBaseCurrency($currency);
}
