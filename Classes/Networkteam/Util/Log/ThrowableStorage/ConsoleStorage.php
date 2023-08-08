<?php

declare(strict_types=1);

namespace Networkteam\Util\Log\ThrowableStorage;

use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\HttpRequestHandlerInterface;
use Neos\Flow\Log\Exception\CouldNotOpenResourceException;
use Neos\Flow\Log\ThrowableStorageInterface;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;

final class ConsoleStorage implements ThrowableStorageInterface
{

	/**
	 * @var false|resource
	 */
	protected $streamHandle;

	/**
	 * @throws CouldNotOpenResourceException
	 */
	public function __construct(string $streamName)
	{
		$this->streamHandle = fopen($streamName, 'w');

		if (!is_resource($this->streamHandle)) {
			throw new CouldNotOpenResourceException(sprintf('Could not open stream "%s" for write access', $streamName), 20230726162106);
		}
	}

	public static function createWithOptions(array $options): ThrowableStorageInterface
	{
		if (!array_key_exists('streamName', $options)) {
			throw new \Exception('A stream name must be set');
		}

		$streamName = $options['streamName'];

		if (strpos($streamName, 'php://') === 0) {
			$streamName = substr($streamName, 6);
		}
		return new static('php://' . $streamName);
	}

	public function logThrowable(\Throwable $throwable, array $additionalData = [])
	{
		// make sure object manager is available
		if (Bootstrap::$staticObjectManager instanceof ObjectManagerInterface) {
			$bootstrap = Bootstrap::$staticObjectManager->get(Bootstrap::class);
			/** @var ConfigurationManager $configurationManager */
			$configurationManager = $bootstrap->getEarlyInstance(ConfigurationManager::class);
			$serviceContext = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Networkteam.Util.serviceContext');
		} else {
			$serviceContext = null;
		}

		$data = [
			'eventTime' => (new \DateTime('now'))->format(DATE_RFC3339),
			'serviceContext' => $serviceContext,
			'message' => sprintf('PHP Warning: %s' . PHP_EOL . 'Stack trace:' . PHP_EOL . '%s', $throwable->getMessage(), $throwable->getTraceAsString()),
			'context' => [
				'httpRequest' => $this->getHttpRequestContext(),
				'reportLocation' => [
					'filePath' => $throwable->getFile(),
					'lineNumber' => $throwable->getLine(),
					'functionName' => self::getFunctionNameForTrace($throwable->getTrace()),
				],
			]
		];

		$output = json_encode(array_filter($data));

		if (is_resource($this->streamHandle)) {
			fwrite($this->streamHandle, $output . PHP_EOL);
		}

		return $output;
	}

	/**
	 * @return mixed[]
	 */
	public function getHttpRequestContext(): array
	{
		if (!(Bootstrap::$staticObjectManager instanceof ObjectManagerInterface)) {
			return [];
		}

		$bootstrap = Bootstrap::$staticObjectManager->get(Bootstrap::class);
		/** @var Bootstrap $bootstrap */
		$requestHandler = $bootstrap->getActiveRequestHandler();
		if (!$requestHandler instanceof HttpRequestHandlerInterface) {
			return [];
		}
		$request = $requestHandler->getHttpRequest();

		$context = [
			'method' => $request->getMethod(),
			'url' => (string)$request->getUri(),
		];

		if ($request->hasHeader('User-Agent')) {
			$context['userAgent'] = $request->getHeader('User-Agent')[0];
		}

		return $context;
	}

	public function setRequestInformationRenderer(\Closure $requestInformationRenderer)
	{
		// No backtrace renderer is needed here
		return $this;
	}

	public function setBacktraceRenderer(\Closure $backtraceRenderer)
	{
		// No backtrace renderer is needed here
		return $this;
	}

	private function getFunctionNameForTrace(?array $trace = null): string
	{
		if ($trace === null) {
			return '<unknown function>';
		}
		if (empty($trace[0]['function'])) {
			return '<none>';
		}
		$functionName = [$trace[0]['function']];
		if (isset($trace[0]['type'])) {
			$functionName[] = $trace[0]['type'];
		}
		if (isset($trace[0]['class'])) {
			$functionName[] = $trace[0]['class'];
		}
		return implode('', array_reverse($functionName));
	}
}
