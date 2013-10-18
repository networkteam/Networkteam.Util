<?php
namespace Networkteam\Util\Converter;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

class OpenExchangeRateConverterService implements CurrencyConverterInterface{

	const CACHEFILENAME = 'openexchange.cache';
	/**
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * @var string
	 */
	protected $appId;

	/**
	 * @var string
	 */
	protected $cacheDir;

	/**
	 * cache timeout in seconds
	 * @var int
	 */
	protected $cacheTimeout;

	/**
	 * @var string
	 */
	protected $baseCurrency;

	/**
	 * @param string $baseUrl
	 * @param string $appId
	 */
	public function __construct($baseUrl, $appId, $cacheDir, $cacheTimeout = 86400, $baseCurrency = 'EUR') {
		$this->baseUrl = $baseUrl;
		$this->appId = $appId;
		$this->cacheDir = $cacheDir;
		$this->cacheTimeout = $cacheTimeout;
		$this->baseCurrency = $baseCurrency;
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
		$convertedCurrencies = $this->getData();
		if(property_exists($convertedCurrencies->rates, $currency)) {
			return $convertedCurrencies->rates->{$currency};
		} else {
			throw new InvalidCurrencyException( $currency . ' is not an available Currency');
		}
	}

	/**
	 * @param float $amount Decimal price
	 * @param string $target
	 *
	 * @return float
	 */
	public function convert($amount, $target) {
		if ($target === $this->baseCurrency) {
			return $amount;
		}
		$baseValue = $amount / $this->getExchangeRate($this->baseCurrency);

		return $baseValue * $this->getExchangeRate($target);
	}

	/**
	 * @return mixed
	 */
	protected function getData() {
		$rawData = $this->fetchRawData();
		return json_decode($rawData);
	}

	/**
	 * return the raw data as provided by the service
	 * @return string
	 */
	protected function fetchRawData() {
		$cacheFile = $this->cacheDir . DIRECTORY_SEPARATOR . self::CACHEFILENAME;
		$cacheTime = file_exists($cacheFile) ? filemtime($cacheFile) : 0;
		$useCache = $cacheTime + $this->cacheTimeout > time();
		if($useCache) {
			$source = $cacheFile;
		} else {
			$source = $this->buildLatestUrl();
		}
		try{
			$rawData = file_get_contents($source);
		} catch(\Exception $e) {
			$useCache = TRUE;
			if (file_exists($cacheFile)) {
				$rawData = file_get_contents($cacheFile);
			} else {
				$rawData = '{}';
			}
		}

		if ($useCache === FALSE) {
			if(!file_exists(dirname($cacheFile))) {
				mkdir(dirname($cacheFile), 0777, TRUE);
				chmod(dirname($cacheFile), 0777);
			}
			file_put_contents($cacheFile, $rawData);
			chmod($cacheFile, 0777);
		}

		return $rawData;
	}

	/**
	 * @return string
	 */
	protected function buildLatestUrl() {
		$baseUrl = $this->baseUrl;
		if(substr($this->baseUrl, -1) !== '/') {
			$baseUrl .= '/';
		}

		return $baseUrl . 'latest.json?app_id=' . $this->appId;
	}

	/**
	 * @param string $appId
	 */
	public function setAppId($appId) {
		$this->appId = $appId;
	}

	/**
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl) {
		$this->baseUrl = $baseUrl;
	}

	/**
	 * @param string $cacheDir
	 */
	public function setCacheDir($cacheDir) {
		$this->cacheDir = $cacheDir;
	}

	/**
	 * @param int $cacheTimeout
	 */
	public function setCacheTimeout($cacheTimeout) {
		$this->cacheTimeout = $cacheTimeout;
	}


}

?>