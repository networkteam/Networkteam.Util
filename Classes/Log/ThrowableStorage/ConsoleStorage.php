<?php

declare(strict_types=1);

namespace Networkteam\Util\Log\ThrowableStorage;

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
            throw new CouldNotOpenResourceException(sprintf('Could not open stream "%s" for write access', $streamName),
                20230726162106);
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
        $data = [
            'eventTime' => (new \DateTime('now'))->format(DATE_RFC3339),
            'severity' => 'critical',
            'message' => $throwable->getMessage(),
            'errorLocation' => [
                'filePath' => $throwable->getFile(),
                'lineNumber' => $throwable->getLine(),
                'functionName' => self::getFunctionNameForTrace($throwable->getTrace()),
            ],
            'httpRequest' => $this->getHttpRequestContext(),
            'additionalData' => $additionalData,
            'source' => 'neos-flow'
        ];

        $output = json_encode($data);

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
